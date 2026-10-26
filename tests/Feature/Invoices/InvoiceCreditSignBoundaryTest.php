<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * GHSA-jvcw-w8pr-j92c: credit-invoice edges of the sign validation and the Mdl_invoice_amounts::calculate()
 * negative-total guard, beyond what InvoiceItemAmountSignTest pins.
 */
#[Group('invoices')]
#[Group('security')]
final class InvoiceCreditSignBoundaryTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoices_due_after', 'setting_value' => '30']);
    }

    #[Test]
    public function it_rejects_a_negative_item_discount_and_a_negative_global_discount_on_a_credit_invoice(): void
    {
        /* Arrange */
        [$creditId, $itemId]          = $this->creditInvoiceWithItem();
        $item                         = $this->existingItem($creditId, $itemId, '-2', '150');
        $item['item_discount_amount'] = '-5';

        /* Act */
        $response = $this->saveItems($creditId, [$item], ['invoice_discount_amount' => '-10', 'invoice_discount_percent' => '-3']);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        self::assertEqualsCanonicalizing(['item_discount_amount', 'invoice_discount_amount', 'invoice_discount_percent'], array_keys($json['validation_errors']));
        $this->assertInvoiceTotal($creditId, '-300.00');
    }

    #[Test]
    public function it_accepts_a_zero_quantity_line_on_a_credit_invoice(): void
    {
        /* Arrange */
        [$creditId, $itemId] = $this->creditInvoiceWithItem();

        /* Act */
        $response = $this->saveItems($creditId, [$this->existingItem($creditId, $itemId, '0', '150')]);

        /* Assert */
        self::assertSame(1, json_decode($response->body(), true)['success'] ?? null, 'Body: ' . $response->body());
        $this->assertInvoiceTotal($creditId, '0.00');
    }

    #[Test]
    public function it_names_the_credit_message_for_a_positive_quantity_on_a_credit_invoice_and_the_plain_one_on_a_regular_invoice(): void
    {
        /* Arrange */
        [$creditId, $creditItem] = $this->creditInvoiceWithItem();
        [$regularId, $regItem]   = $this->regularInvoiceWithItem();

        /* Act */
        $credit  = json_decode($this->saveItems($creditId, [$this->existingItem($creditId, $creditItem, '1', '150')])->body(), true);
        $regular = json_decode($this->saveItems($regularId, [$this->existingItem($regularId, $regItem, '-1', '150')])->body(), true);

        /* Assert */
        self::assertSame('Quantities on a credit invoice must be zero or negative.', $credit['validation_errors']['item_quantity'] ?? null);
        self::assertSame('The item quantity cannot be negative.', $regular['validation_errors']['item_quantity'] ?? null);
    }

    #[Test]
    public function it_keeps_the_stored_amounts_of_a_regular_invoice_intact_when_the_negative_total_guard_trips(): void
    {
        /* Arrange */
        [$invoiceId] = $this->regularInvoiceWithItem();
        $this->smuggleBadRow($invoiceId);
        $small  = $this->databaseInsertSmallRow($invoiceId);
        $before = $this->databaseFetchOne('ip_invoice_amounts', ['invoice_id' => $invoiceId]);

        /* Act */
        $this->ajax('POST', '/invoices/ajax/delete_item/' . $invoiceId, ['item_id' => (string) $small]);

        /* Assert */
        $after = $this->databaseFetchOne('ip_invoice_amounts', ['invoice_id' => $invoiceId]);
        self::assertSame($before['invoice_total'], $after['invoice_total']);
        self::assertSame($before['invoice_balance'], $after['invoice_balance']);
        self::assertSame($before['invoice_item_subtotal'], $after['invoice_item_subtotal']);
    }

    #[Test]
    public function it_lets_the_same_recalculation_store_a_negative_total_for_a_credit_invoice(): void
    {
        /* Arrange: identical rows, but the invoice is a credit invoice (positive control for the guard) */
        [$invoiceId] = $this->regularInvoiceWithItem();
        $this->smuggleBadRow($invoiceId);
        $small = $this->databaseInsertSmallRow($invoiceId);
        $this->databaseUpdate('ip_invoice_amounts', ['invoice_sign' => -1], ['invoice_id' => $invoiceId]);

        /* Act */
        $this->ajax('POST', '/invoices/ajax/delete_item/' . $invoiceId, ['item_id' => (string) $small]);

        /* Assert: 300 - 500 */
        $this->assertInvoiceTotal($invoiceId, '-200.00');
    }

    #[Test]
    public function it_lets_the_recalculation_store_a_negative_total_when_the_invoice_credits_a_parent(): void
    {
        /* Arrange */
        [$invoiceId] = $this->regularInvoiceWithItem();
        $this->smuggleBadRow($invoiceId);
        $small = $this->databaseInsertSmallRow($invoiceId);
        $this->databaseUpdate('ip_invoices', ['creditinvoice_parent_id' => 1], ['invoice_id' => $invoiceId]);

        /* Act */
        $this->ajax('POST', '/invoices/ajax/delete_item/' . $invoiceId, ['item_id' => (string) $small]);

        /* Assert */
        $this->assertInvoiceTotal($invoiceId, '-200.00');
    }

    // ---- helpers -------------------------------------------------------------------------------

    /** @return array{0: int, 1: int} */
    private function regularInvoiceWithItem(): array
    {
        $invoiceId = $this->seedInvoice($this->seedClient(), ['invoice_number' => 'CSB-' . bin2hex(random_bytes(3))]);
        $response  = $this->saveItems($invoiceId, [$this->newItem($invoiceId, 'Widget', '2', '150')]);
        self::assertSame(1, json_decode($response->body(), true)['success'] ?? null, 'Setup save failed: ' . $response->body());

        return [$invoiceId, (int) $this->databaseFetchOne('ip_invoice_items', ['invoice_id' => $invoiceId, 'item_name' => 'Widget'])['item_id']];
    }

    /** @return array{0: int, 1: int} */
    private function creditInvoiceWithItem(): array
    {
        [$sourceId] = $this->regularInvoiceWithItem();
        $response   = $this->ajax('POST', '/invoices/ajax/create_credit', [
            'client_id'            => (string) $this->databaseFetchOne('ip_invoices', ['invoice_id' => $sourceId])['client_id'],
            'invoice_date_created' => date('Y-m-d'), 'invoice_time_created' => date('H:i:s'), 'invoice_group_id' => '1', 'user_id' => '1',
            'invoice_id'           => (string) $sourceId,
        ]);
        $creditId = (int) json_decode($response->body(), true)['invoice_id'];

        return [$creditId, (int) $this->databaseFetchOne('ip_invoice_items', ['invoice_id' => $creditId])['item_id']];
    }

    private function databaseInsertSmallRow(int $invoiceId): int
    {
        $itemId = $this->databaseInsert('ip_invoice_items', [
            'invoice_id' => $invoiceId, 'item_name' => 'Small', 'item_description' => '', 'item_quantity' => '1', 'item_price' => '10',
            'item_order' => 2, 'item_date_added' => date('Y-m-d'),
        ]);
        $this->databaseInsert('ip_invoice_item_amounts', ['item_id' => $itemId, 'item_subtotal' => '10.00', 'item_tax_total' => '0.00', 'item_discount' => '0.00', 'item_total' => '10.00']);

        return $itemId;
    }

    private function smuggleBadRow(int $invoiceId): void
    {
        $bad = $this->databaseInsert('ip_invoice_items', [
            'invoice_id' => $invoiceId, 'item_name' => 'Smuggled', 'item_description' => '', 'item_quantity' => '-10', 'item_price' => '50',
            'item_order' => 3, 'item_date_added' => date('Y-m-d'),
        ]);
        $this->databaseInsert('ip_invoice_item_amounts', ['item_id' => $bad, 'item_subtotal' => '-500.00', 'item_tax_total' => '0.00', 'item_discount' => '0.00', 'item_total' => '-500.00']);
    }

    /**
     * @param list<array<string, string>> $items
     * @param array<string, string>       $extra
     */
    private function saveItems(int $invoiceId, array $items, array $extra = [])
    {
        return $this->ajax('POST', '/invoices/ajax/save', array_merge([
            'invoice_id'               => (string) $invoiceId,
            'invoice_number'           => 'CSB-' . $invoiceId,
            'invoice_date_created'     => date('Y-m-d'),
            'invoice_date_due'         => date('Y-m-d', strtotime('+30 days')),
            'invoice_time_created'     => date('H:i:s'),
            'invoice_status_id'        => '2',
            'invoice_discount_percent' => '0',
            'invoice_discount_amount'  => '0',
            'items'                    => json_encode($items),
        ], $extra));
    }

    /** @return array<string, string> */
    private function newItem(int $invoiceId, string $name, string $quantity, string $price): array
    {
        return [
            'invoice_id'   => (string) $invoiceId, 'item_id' => '', 'item_name' => $name, 'item_description' => '', 'item_quantity' => $quantity,
            'item_price'   => $price, 'item_discount_amount' => '', 'item_product_id' => '', 'item_product_unit_id' => '',
            'item_task_id' => '', 'item_tax_rate_id' => '0',
        ];
    }

    /** @return array<string, string> */
    private function existingItem(int $invoiceId, int $itemId, string $quantity, string $price): array
    {
        return ['item_id' => (string) $itemId] + $this->newItem($invoiceId, 'Widget', $quantity, $price);
    }

    private function assertInvoiceTotal(int $invoiceId, string $total): void
    {
        self::assertSame($total, $this->databaseFetchOne('ip_invoice_amounts', ['invoice_id' => $invoiceId])['invoice_total'] ?? null);
    }
}
