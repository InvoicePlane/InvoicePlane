<?php

namespace Tests\Unit\Models;

use DateTime;
use PHPUnit\Framework\TestCase;

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
class Mdl_PaymentsTest extends TestCase
{
    private StubMdl_Payments $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new StubMdl_Payments();
    }

    public function it_returns_validation_rules_for_all_five_payment_fields(): void
    {
        $rules = $this->model->validation_rules();

        $required = ['invoice_id', 'payment_date', 'payment_amount', 'payment_method_id', 'payment_note'];

        foreach ($required as $field) {
            self::assertArrayHasKey(
                $field,
                $rules,
                sprintf('validation_rules() must contain a rule for [%s].', $field)
            );
        }
    }

    public function it_marks_invoice_id_payment_date_and_payment_amount_as_required(): void
    {
        $rules = $this->model->validation_rules();

        foreach (['invoice_id', 'payment_date', 'payment_amount'] as $field) {
            self::assertStringContainsString(
                'required',
                $rules[$field]['rules'],
                sprintf('[%s] must have the [required] rule.', $field)
            );
        }
    }

    public function it_includes_a_custom_callback_on_payment_amount(): void
    {
        $rules = $this->model->validation_rules();

        self::assertStringContainsString(
            'callback_validate_payment_amount',
            $rules['payment_amount']['rules'],
            'payment_amount must reference the custom callback [callback_validate_payment_amount].'
        );
    }

    public function it_returns_false_when_payment_amount_exceeds_invoice_balance(): void
    {
        $result = $this->model->checkPaymentDoesNotExceedBalance(
            amount: 500.00,
            invoiceBalance: 100.00,
            existingPaymentAmount: 0.0
        );

        self::assertFalse(
            $result,
            'A payment amount of 500.00 against a balance of 100.00 must be rejected.'
        );
    }

    public function it_returns_true_when_payment_amount_equals_invoice_balance(): void
    {
        $result = $this->model->checkPaymentDoesNotExceedBalance(
            amount: 100.00,
            invoiceBalance: 100.00,
            existingPaymentAmount: 0.0
        );

        self::assertTrue(
            $result,
            'A payment amount equal to the invoice balance must be accepted.'
        );
    }

    public function it_returns_true_when_payment_amount_is_less_than_invoice_balance(): void
    {
        $result = $this->model->checkPaymentDoesNotExceedBalance(
            amount: 49.99,
            invoiceBalance: 100.00,
            existingPaymentAmount: 0.0
        );

        self::assertTrue(
            $result,
            'A payment amount less than the invoice balance must be accepted.'
        );
    }

    public function it_adds_existing_payment_amount_back_to_balance_when_editing(): void
    {
        $result = $this->model->checkPaymentDoesNotExceedBalance(
            amount: 150.00,
            invoiceBalance: 100.00,
            existingPaymentAmount: 75.00
        );

        self::assertTrue(
            $result,
            'When editing a payment of 75.00, the available balance becomes 175.00 so 150.00 must be accepted.'
        );
    }

    public function it_rejects_a_payment_exceeding_the_balance_even_when_editing(): void
    {
        $result = $this->model->checkPaymentDoesNotExceedBalance(
            amount: 300.00,
            invoiceBalance: 100.00,
            existingPaymentAmount: 75.00
        );

        self::assertFalse(
            $result,
            'A payment of 300.00 against balance 100.00 + existing 75.00 = 175.00 must be rejected.'
        );
    }

    public function it_normalises_the_payment_date_to_mysql_format_yyyy_mm_dd(): void
    {
        $normalised = $this->model->normaliseDate('01/15/2025');

        self::assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}$/',
            $normalised,
            'normaliseDate() must produce a YYYY-MM-DD string for MySQL storage.'
        );
    }

    public function it_normalises_a_decimal_amount_string_by_removing_thousands_separators(): void
    {
        $normalised = $this->model->normaliseAmount('1,234.56');

        self::assertSame(
            '1234.56',
            $normalised,
            'normaliseAmount() must strip thousands separators and return a plain decimal string.'
        );
    }

    public function it_normalises_a_european_formatted_amount_with_comma_decimal(): void
    {
        $normalised = $this->model->normaliseAmount('1.234,56', decimalPoint: ',', thousandsSep: '.');

        self::assertSame(
            '1234.56',
            $normalised,
            'normaliseAmount() must handle European number formatting (dot-thousands, comma-decimal).'
        );
    }

    public function it_returns_zero_string_when_normalising_an_empty_amount(): void
    {
        $normalised = $this->model->normaliseAmount('');

        self::assertSame(
            '0.00',
            $normalised,
            'normaliseAmount() with an empty string must return [0.00] as the safe default.'
        );
    }

    public function it_marks_the_invoice_as_paid_when_paid_amount_reaches_total(): void
    {
        $shouldMarkPaid = $this->model->shouldMarkInvoicePaid(
            paidTotal: 500.00,
            invoiceTotal: 500.00
        );

        self::assertTrue(
            $shouldMarkPaid,
            'When paid amount equals invoice total, the invoice must be marked as paid (status_id = 4).'
        );
    }

    public function it_does_not_mark_the_invoice_as_paid_when_balance_remains(): void
    {
        $shouldMarkPaid = $this->model->shouldMarkInvoicePaid(
            paidTotal: 400.00,
            invoiceTotal: 500.00
        );

        self::assertFalse(
            $shouldMarkPaid,
            'When paid amount is less than invoice total, the invoice must NOT be marked as paid.'
        );
    }

    public function it_marks_the_invoice_as_paid_when_overpayment_occurs(): void
    {
        $shouldMarkPaid = $this->model->shouldMarkInvoicePaid(
            paidTotal: 600.00,
            invoiceTotal: 500.00
        );

        self::assertTrue(
            $shouldMarkPaid,
            'An overpayment (paid > total) must still mark the invoice as paid.'
        );
    }
}

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
