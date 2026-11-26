<?php

namespace Tests\Feature\Invoices;

use Mdl_Invoice_Amounts;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Mdl_Invoice_Amounts::calculate() — the totals every invoice, payment and report relies on.
 * Each expectation below is computed by hand (legacy calculation, 2 decimals), then checked
 * against what the save endpoint persisted in ip_invoice_amounts.
 */
#[CoversClass(Mdl_Invoice_Amounts::class)]
final class InvoiceAmountsCalculationTest extends AbstractTestCase
{
    private int $clientId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->setSetting('tax_rate_decimal_places', '2');
        $this->clientId = $this->seedClient();
    }

    #[Test]
    public function it_sums_item_subtotals_and_item_level_tax(): void
    {
        /* Arrange: 2 x 50 taxed at 21% (tax 21) + 1 x 30 untaxed */
        $vat       = $this->taxRate('VAT 21', '21.00');
        $invoiceId = $this->invoice();

        /* Act */
        $this->save($invoiceId, [$this->item($invoiceId, 'Taxed', '2', '50', $vat), $this->item($invoiceId, 'Untaxed', '1', '30')]);

        /* Assert */
        $this->assertAmounts($invoiceId, subtotal: 130.00, itemTax: 21.00, total: 151.00, paid: 0.00, balance: 151.00);
    }

    #[Test]
    public function it_applies_a_global_percent_discount_to_subtotal_plus_tax(): void
    {
        /* Arrange: 151.00 gross, 10% off -> 151 - round(15.10) = 135.90 */
        $vat       = $this->taxRate('VAT 21', '21.00');
        $invoiceId = $this->invoice();

        /* Act */
        $this->save($invoiceId, [$this->item($invoiceId, 'Taxed', '2', '50', $vat), $this->item($invoiceId, 'Untaxed', '1', '30')], ['invoice_discount_percent' => '10']);

        /* Assert */
        $this->assertAmounts($invoiceId, subtotal: 130.00, itemTax: 21.00, total: 135.90, paid: 0.00, balance: 135.90);
    }

    #[Test]
    public function it_applies_a_global_fixed_discount(): void
    {
        /* Arrange: 130.00 - 20.00 = 110.00 (no tax) */
        $invoiceId = $this->invoice();

        /* Act */
        $this->save($invoiceId, [$this->item($invoiceId, 'Plain', '2', '65')], ['invoice_discount_amount' => '20']);

        /* Assert */
        $this->assertAmounts($invoiceId, subtotal: 130.00, itemTax: 0.00, total: 110.00, paid: 0.00, balance: 110.00);
    }

    #[Test]
    public function it_deducts_payments_from_the_balance_without_settling_a_part_paid_invoice(): void
    {
        /* Arrange: 100.00 invoice, 40.00 already paid */
        $invoiceId = $this->invoice(['invoice_status_id' => 2]);
        $this->seedPayment($invoiceId, ['payment_amount' => '40.00']);

        /* Act */
        $this->save($invoiceId, [$this->item($invoiceId, 'Plain', '1', '100')]);

        /* Assert */
        $this->assertAmounts($invoiceId, subtotal: 100.00, itemTax: 0.00, total: 100.00, paid: 40.00, balance: 60.00);
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'invoice_status_id' => 2]);
    }

    #[Test]
    public function it_marks_the_invoice_paid_when_payments_cover_the_total(): void
    {
        /* Arrange */
        $invoiceId = $this->invoice(['invoice_status_id' => 2]);
        $this->seedPayment($invoiceId, ['payment_amount' => '100.00', 'payment_method_id' => 3]);

        /* Act */
        $this->save($invoiceId, [$this->item($invoiceId, 'Plain', '1', '100')]);

        /* Assert: balance zero -> status 4 (paid), payment method copied from the payment */
        $this->assertAmounts($invoiceId, subtotal: 100.00, itemTax: 0.00, total: 100.00, paid: 100.00, balance: 0.00);
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'invoice_status_id' => 4, 'payment_method' => 3]);
    }

    #[Test]
    public function it_does_not_mark_a_zero_total_invoice_as_paid(): void
    {
        /* Arrange: nothing billable yet, so the balance is zero but there is nothing to be "paid" */
        $invoiceId = $this->invoice(['invoice_status_id' => 2]);

        /* Act */
        $this->save($invoiceId, []);

        /* Assert */
        $this->assertAmounts($invoiceId, subtotal: 0.00, itemTax: 0.00, total: 0.00, paid: 0.00, balance: 0.00);
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'invoice_status_id' => 2]);
    }

    #[Test]
    public function it_adds_an_invoice_level_tax_on_the_item_subtotal_only(): void
    {
        /* Arrange: 130.00 subtotal, item tax 21.00, plus a 10% invoice tax that excludes item tax (13.00) */
        $vat       = $this->taxRate('VAT 21', '21.00');
        $surcharge = $this->taxRate('Surcharge 10', '10.00');
        $invoiceId = $this->invoice();
        $items     = [$this->item($invoiceId, 'Taxed', '2', '50', $vat), $this->item($invoiceId, 'Untaxed', '1', '30')];
        $this->save($invoiceId, $items);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/save_invoice_tax_rate', [
            'invoice_id' => (string) $invoiceId, 'tax_rate_id' => (string) $surcharge, 'include_item_tax' => '0',
        ]);
        $this->save($invoiceId, []); // recalculate only; re-posting items without an item_id would insert them twice

        /* Assert: 130 + 21 + 13 = 164.00 */
        self::assertSame(1, json_decode($response->body(), true)['success'] ?? null, $response->body());
        $this->assertDatabaseHas('ip_invoice_tax_rates', ['invoice_id' => $invoiceId, 'tax_rate_id' => $surcharge, 'invoice_tax_rate_amount' => '13.00']);
        $row = $this->databaseFetchOne('ip_invoice_amounts', ['invoice_id' => $invoiceId]);
        self::assertEqualsWithDelta(13.00, (float) $row['invoice_tax_total'], 0.001);
        self::assertEqualsWithDelta(164.00, (float) $row['invoice_total'], 0.001);
    }

    #[Test]
    public function it_taxes_item_tax_too_when_the_invoice_tax_includes_it(): void
    {
        /* Arrange: 10% on (130 + 21) = 15.10 -> total 130 + 21 + 15.10 = 166.10 */
        $vat       = $this->taxRate('VAT 21', '21.00');
        $surcharge = $this->taxRate('Surcharge 10', '10.00');
        $invoiceId = $this->invoice();
        $items     = [$this->item($invoiceId, 'Taxed', '2', '50', $vat), $this->item($invoiceId, 'Untaxed', '1', '30')];
        $this->save($invoiceId, $items);

        /* Act */
        $this->ajax('POST', '/invoices/ajax/save_invoice_tax_rate', ['invoice_id' => (string) $invoiceId, 'tax_rate_id' => (string) $surcharge, 'include_item_tax' => '1']);
        $this->save($invoiceId, []);

        /* Assert */
        $row = $this->databaseFetchOne('ip_invoice_amounts', ['invoice_id' => $invoiceId]);
        self::assertEqualsWithDelta(166.10, (float) $row['invoice_total'], 0.001);
    }

    #[Test]
    public function it_keeps_each_invoices_totals_separate(): void
    {
        /* Arrange */
        $first  = $this->invoice();
        $second = $this->invoice();

        /* Act */
        $this->save($first, [$this->item($first, 'A', '1', '10')]);
        $this->save($second, [$this->item($second, 'B', '1', '99')]);

        /* Assert */
        $this->assertAmounts($first, subtotal: 10.00, itemTax: 0.00, total: 10.00, paid: 0.00, balance: 10.00);
        $this->assertAmounts($second, subtotal: 99.00, itemTax: 0.00, total: 99.00, paid: 0.00, balance: 99.00);
    }

    // -------------------------------------------------------------------------

    /** @param array<string,mixed> $overrides */
    private function invoice(array $overrides = []): int
    {
        return $this->seedInvoice($this->clientId, $overrides);
    }

    private function taxRate(string $name, string $percent): int
    {
        return $this->databaseInsert('ip_tax_rates', ['tax_rate_name' => $name, 'tax_rate_percent' => $percent]);
    }

    private function setSetting(string $key, string $value): void
    {
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => $key, 'setting_value' => $value]);
        $this->databaseUpdate('ip_settings', ['setting_value' => $value], ['setting_key' => $key]);
    }

    /** @return array<string,string> */
    private function item(int $invoiceId, string $name, string $quantity, string $price, ?int $taxRateId = null): array
    {
        return [
            'invoice_id'           => (string) $invoiceId, 'item_id' => '', 'item_name' => $name, 'item_description' => '',
            'item_quantity'        => $quantity, 'item_price' => $price, 'item_discount_amount' => '', 'item_product_id' => '',
            'item_product_unit_id' => '', 'item_tax_rate_id' => (string) ($taxRateId ?? 0),
        ];
    }

    /**
     * @param list<array<string,string>> $items
     * @param array<string,string>       $overrides
     */
    private function save(int $invoiceId, array $items, array $overrides = []): void
    {
        $response = $this->ajax('POST', '/invoices/ajax/save', array_merge([
            'invoice_id'               => (string) $invoiceId, 'invoice_date_created' => date('Y-m-d'),
            'invoice_date_due'         => date('Y-m-d', strtotime('+30 days')), 'invoice_time_created' => date('H:i:s'),
            'invoice_status_id'        => (string) $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_status_id'],
            'invoice_discount_percent' => '0', 'invoice_discount_amount' => '0', 'items' => json_encode($items),
        ], $overrides));

        self::assertSame(1, json_decode($response->body(), true)['success'] ?? null, 'Save failed: ' . $response->body());
    }

    private function assertAmounts(int $invoiceId, float $subtotal, float $itemTax, float $total, float $paid, float $balance): void
    {
        $row = $this->databaseFetchOne('ip_invoice_amounts', ['invoice_id' => $invoiceId]);
        self::assertNotNull($row, 'No ip_invoice_amounts row was written.');
        self::assertEqualsWithDelta($subtotal, (float) $row['invoice_item_subtotal'], 0.001, 'item subtotal');
        self::assertEqualsWithDelta($itemTax, (float) $row['invoice_item_tax_total'], 0.001, 'item tax total');
        self::assertEqualsWithDelta($total, (float) $row['invoice_total'], 0.001, 'invoice total');
        self::assertEqualsWithDelta($paid, (float) $row['invoice_paid'], 0.001, 'paid');
        self::assertEqualsWithDelta($balance, (float) $row['invoice_balance'], 0.001, 'balance');
    }
}
