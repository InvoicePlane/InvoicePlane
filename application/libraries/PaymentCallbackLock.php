<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Connection-scoped MySQL advisory lock serializing concurrent payment writers
 * for the same invoice (CWE-362/367 TOCTOU): the admin payment form/ajax
 * endpoints read invoice_balance in Mdl_Payments::validate_payment_amount(),
 * decide to record a payment, then save it via Mdl_Payments::save() as
 * separate non-atomic steps. Two admin sessions submitting a payment for the
 * same invoice at the same time can both pass the balance check before
 * either commits, over-crediting the invoice.
 *
 * Blocks (rather than failing fast) because the loser must wait for the
 * winner to commit, then re-check the now up-to-date balance and correctly
 * reject instead of over-crediting.
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
