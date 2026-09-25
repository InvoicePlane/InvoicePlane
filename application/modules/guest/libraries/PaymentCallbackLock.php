<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Connection-scoped MySQL advisory lock serializing concurrent gateway callbacks
 * for the same invoice (CWE-362/367 TOCTOU): the guest-facing Stripe/PayPal
 * callbacks read invoice_balance, decide to record a payment, then save it as
 * separate non-atomic steps. Two callbacks racing on the same invoice can both
 * pass the balance check before either commits, double-recording the payment.
 *
 * Blocks (rather than failing fast like IntegrationSyncLock) because the loser
 * must wait for the winner to commit, then re-check the now up-to-date balance
 * and correctly no-op as "already paid" instead of erroring out.
 */
final class PaymentCallbackLock
{
    private ?string $name = null;

    public function __construct(private object $database) {}

    public function __destruct()
    {
        $this->release();
    }

    public function acquire(int $invoiceId, int $timeoutSeconds = 10): bool
    {
        if ($this->name !== null) {
            throw new LogicException('Payment callback lock is already held.');
        }

        $name = 'ip:payment:invoice:' . $invoiceId;
        $row  = $this->database
            ->query('SELECT GET_LOCK(?, ?) AS acquired', [$name, $timeoutSeconds])
            ->row_array();

        if ((int) ($row['acquired'] ?? 0) !== 1) {
            return false;
        }

        $this->name = $name;

        return true;
    }

    public function release(): void
    {
        if ($this->name === null) {
            return;
        }

        try {
            $this->database->query('SELECT RELEASE_LOCK(?)', [$this->name]);
        } catch (Throwable) {
            // A lost database connection releases MySQL advisory locks itself.
        } finally {
            $this->name = null;
        }
    }
}
