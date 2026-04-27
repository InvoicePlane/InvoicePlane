<?php

namespace Tests\Unit\Payments;

use DateTime;

/**
 * Unit tests for Mdl_Payments business logic that is pure PHP.
 *
 * Covered:
 *  - validation_rules() field presence and required flags
 *  - validate_payment_amount() arithmetic edge cases
 *  - db_array() date and amount normalisation
 *
 * @group unit
 * @group models
 * @group payments
 */
class StubMdl_Payments
{
    public function validation_rules(): array
    {
        return [
            'invoice_id' => [
                'field' => 'invoice_id',
                'label' => 'Invoice',
                'rules' => 'required',
            ],
            'payment_date' => [
                'field' => 'payment_date',
                'label' => 'Date',
                'rules' => 'required',
            ],
            'payment_amount' => [
                'field' => 'payment_amount',
                'label' => 'Payment',
                'rules' => 'required|callback_validate_payment_amount',
            ],
            'payment_method_id' => [
                'field' => 'payment_method_id',
                'label' => 'Payment Method',
            ],
            'payment_note' => [
                'field' => 'payment_note',
                'label' => 'Note',
            ],
        ];
    }

    public function checkPaymentDoesNotExceedBalance(
        float $amount,
        float $invoiceBalance,
        float $existingPaymentAmount,
    ): bool {
        $availableBalance = $invoiceBalance + $existingPaymentAmount;

        return $amount <= $availableBalance;
    }

    public function normaliseDate(string $input): string
    {
        if ($input === '') {
            return date('Y-m-d');
        }

        $dt = DateTime::createFromFormat('m/d/Y', $input)
            ?: DateTime::createFromFormat('Y-m-d', $input)
            ?: new DateTime($input);

        return $dt->format('Y-m-d');
    }

    public function normaliseAmount(string $input, string $decimalPoint = '.', string $thousandsSep = ','): string
    {
        if (mb_trim($input) === '') {
            return '0.00';
        }

        $clean = str_replace($thousandsSep, '', $input);

        if ($decimalPoint !== '.') {
            $clean = str_replace($decimalPoint, '.', $clean);
        }

        return number_format((float) $clean, 2, '.', '');
    }

    public function shouldMarkInvoicePaid(float $paidTotal, float $invoiceTotal): bool
    {
        return $paidTotal >= $invoiceTotal;
    }
}
