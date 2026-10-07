<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Connection-scoped MySQL advisory lock serializing concurrent payment writers
 * for the same invoice (CWE-362/367 TOCTOU): both the guest-facing Stripe/PayPal
 * callbacks and the admin payment form/ajax endpoints read invoice_balance,
 * decide to record a payment, then save it as separate non-atomic steps. Two
 * writers racing on the same invoice — two gateway callbacks, or two admin
 * sessions submitting a payment concurrently — can both pass the balance check
 * before either commits, over-crediting the invoice.
 *
 * Blocks (rather than failing fast like IntegrationSyncLock) because the loser
 * must wait for the winner to commit, then re-check the now up-to-date balance
 * and correctly reject/no-op instead of over-crediting.
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
        $acquired = $row['acquired'] ?? null;

        // GET_LOCK() returns NULL only on a server-side error (e.g. the lock
        // request was killed, or the connection ran out of resources) -- never
        // as the ordinary "someone else is holding it" outcome. Surface that as
        // a real error instead of silently treating it the same as a timeout.
        if ($acquired === null) {
            throw new RuntimeException('GET_LOCK() failed for invoice lock ' . $name . '.');
        }

        if ((int) $acquired !== 1) {
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
