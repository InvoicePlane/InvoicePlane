<?php

namespace Tests\Unit\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\UsesCodeIgniterModels;

/**
 * GHSA-jvcw-w8pr-j92c: Mdl_invoice_amounts::calculate() refuses to store a negative total for a regular
 * invoice. Its callers read the refusal from the boolean it returns and from negative_total_refused;
 * the HTTP-level tests only look at the stored amounts, so a guard that stored nothing but reported
 * success (return true) was never noticed.
 */
final class InvoiceAmountsNegativeGuardReturnTest extends AbstractTestCase
{
    use UsesCodeIgniterModels;

    protected function setUp(): void
    {
        parent::setUp();
        require_once APPPATH . 'core/Password_Encryption_Trait.php';
    }

    protected function tearDown(): void
    {
        $this->tearDownCodeIgniter();
        parent::tearDown();
    }

    #[Test]
    public function it_reports_a_refusal_for_a_negative_total_on_a_regular_invoice(): void
    {
        /* Arrange */
        $invoiceId = $this->seedInvoice($this->seedClient());
        $this->seedItem($invoiceId, '-500.00');
        $model = $this->model();

        /* Act */
        $result = $model->calculate($invoiceId, ['item' => 0.0, 'amount' => 0.0]);

        /* Assert */
        self::assertFalse($result);
        self::assertTrue($model->negative_total_refused);
    }

    #[Test]
    public function it_reports_success_and_stores_the_amounts_for_a_positive_total(): void
    {
        /* Arrange: positive control, same shape with a positive line */
        $invoiceId = $this->seedInvoice($this->seedClient());
        $this->seedItem($invoiceId, '500.00');
        $model = $this->model();

        /* Act */
        $result = $model->calculate($invoiceId, ['item' => 0.0, 'amount' => 0.0]);

        /* Assert */
        self::assertTrue($result);
        self::assertFalse($model->negative_total_refused);
        $this->assertDatabaseHas('ip_invoice_amounts', ['invoice_id' => $invoiceId, 'invoice_total' => '500.00']);
    }

    private function model(): object
    {
        $ci = $this->bootCodeIgniter();
        $ci->load->helper(['date', 'settings']);
        $ci->load->model('settings/mdl_settings');
        $ci->mdl_settings->load_settings();
        $ci->load->model('invoices/mdl_invoice_amounts');

        return $ci->mdl_invoice_amounts;
    }

    private function seedItem(int $invoiceId, string $subtotal): void
    {
        $itemId = $this->databaseInsert('ip_invoice_items', [
            'invoice_id' => $invoiceId, 'item_name' => 'Line', 'item_description' => '', 'item_quantity' => '1', 'item_price' => $subtotal,
            'item_order' => 1, 'item_date_added' => date('Y-m-d'),
        ]);
        $this->databaseInsert('ip_invoice_item_amounts', [
            'item_id' => $itemId, 'item_subtotal' => $subtotal, 'item_tax_total' => '0.00', 'item_discount' => '0.00', 'item_total' => $subtotal,
        ]);
    }
}
