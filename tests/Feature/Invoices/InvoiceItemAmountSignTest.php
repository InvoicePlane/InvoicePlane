<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Sign validation on invoice save: a negative quantity, price or discount used to be stored and
 * summed as-is, so (-3) x (-150) inflated a 300.00 invoice to 750.00 and (-10) x 50 turned it into a
 * -200.00 bill. Every rejection below must also leave the stored invoice untouched.
 */
#[Group('invoices')]
#[Group('security')]
final class InvoiceItemAmountSignTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoices_due_after', 'setting_value' => '30']);
    }

    /** @return array<string, array{string, string, string, string}> */
    public static function negativeAmounts(): array
    {
        return [
            'negative quantity'  => ['-1', '10', '', 'item_quantity'],
            'negative price'     => ['1', '-10', '', 'item_price'],
            'negative discount'  => ['1', '10', '-5', 'item_discount_amount'],
            'localised negative' => ['-1,5', '10', '', 'item_quantity'],
        ];
    }

    /** @return array<string, array{string, string}> */
    public static function smallNonNegativeTotals(): array
    {
        return ['zero' => ['0', '0.00'], 'one unit' => ['1', '1.00']];
    }

    /** @return array<string, array{array<string, string>}> */
    public static function discountsLargerThanTheInvoice(): array
    {
        return [
            'amount above the subtotal' => [['invoice_discount_amount' => '700']],
            'percent above one hundred' => [['invoice_discount_percent' => '150']],
        ];
    }

    // ---- the two reported attacks --------------------------------------------------------------

    #[Test]
    public function it_rejects_the_double_negative_that_inflates_the_total(): void
    {
        /* Arrange: a 300.00 invoice (2 x 150) */
        [$invoiceId, $itemId] = $this->invoiceWithOneItem();

        /* Act: add a line of -3 x -150 = +450 */
        $response = $this->saveItems($invoiceId, [$this->existingItem($invoiceId, $itemId), $this->newItem($invoiceId, 'Adjustment', '-3', '-150')]);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        self::assertSame('The item quantity cannot be negative.', $json['validation_errors']['item_quantity'] ?? null);
        self::assertSame('The item price cannot be negative.', $json['validation_errors']['item_price'] ?? null);
        $this->assertDatabaseMissing('ip_invoice_items', ['invoice_id' => $invoiceId, 'item_name' => 'Adjustment']);
        $this->assertInvoiceTotal($invoiceId, '300.00');
    }

    #[Test]
    public function it_rejects_the_hidden_credit_line_that_makes_the_bill_negative(): void
    {
        [$invoiceId, $itemId] = $this->invoiceWithOneItem();

        $response = $this->saveItems($invoiceId, [$this->existingItem($invoiceId, $itemId), $this->newItem($invoiceId, 'Adjustment', '-10', '50')]);

        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        self::assertArrayHasKey('item_quantity', $json['validation_errors']);
        self::assertArrayNotHasKey('item_price', $json['validation_errors']);
        $this->assertDatabaseMissing('ip_invoice_items', ['invoice_id' => $invoiceId, 'item_name' => 'Adjustment']);
        $this->assertInvoiceTotal($invoiceId, '300.00');
    }

    #[Test]
    #[DataProvider('negativeAmounts')]
    public function it_rejects_each_negative_amount_on_its_own(string $quantity, string $price, string $discount, string $field): void
    {
        [$invoiceId, $itemId] = $this->invoiceWithOneItem();

        $response = $this->saveItems($invoiceId, [$this->existingItem($invoiceId, $itemId), $this->newItem($invoiceId, 'Extra', $quantity, $price, $discount)]);

        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        self::assertSame([$field], array_keys($json['validation_errors']));
        $this->assertDatabaseMissing('ip_invoice_items', ['invoice_id' => $invoiceId, 'item_name' => 'Extra']);
        $this->assertInvoiceTotal($invoiceId, '300.00');
    }

    #[Test]
    public function it_rejects_a_negative_global_discount_amount_and_percent(): void
    {
        [$invoiceId, $itemId] = $this->invoiceWithOneItem();

        $response = $this->saveItems($invoiceId, [$this->existingItem($invoiceId, $itemId)], ['invoice_discount_amount' => '-50', 'invoice_discount_percent' => '-10']);

        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        self::assertSame('The discount cannot be negative.', $json['validation_errors']['invoice_discount_amount'] ?? null);
        self::assertSame('The discount cannot be negative.', $json['validation_errors']['invoice_discount_percent'] ?? null);
        $this->assertInvoiceTotal($invoiceId, '300.00');
    }

    #[Test]
    #[DataProvider('discountsLargerThanTheInvoice')]
    public function it_rolls_back_the_whole_save_when_the_discounts_would_make_the_total_negative(array $discount): void
    {
        [$invoiceId, $itemId] = $this->invoiceWithOneItem();

        $response = $this->saveItems($invoiceId, [$this->existingItem($invoiceId, $itemId, '3', '200')], $discount);

        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null, 'Body: ' . $response->body());
        self::assertSame('The discounts exceed the invoice total, so the total would be negative.', $json['validation_errors']['invoice_total'] ?? null);
        self::assertSame('150', rtrim(rtrim($this->databaseFetchOne('ip_invoice_items', ['item_id' => $itemId])['item_price'], '0'), '.'), 'the item edit must be rolled back');
        $header = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        self::assertSame('0.00', $header['invoice_discount_amount']);
        self::assertSame('0.00', $header['invoice_discount_percent']);
        $this->assertInvoiceTotal($invoiceId, '300.00');
    }

    #[Test]
    public function it_rolls_back_earlier_items_when_a_later_line_has_no_name(): void
    {
        [$invoiceId, $itemId] = $this->invoiceWithOneItem();

        $response = $this->saveItems($invoiceId, [$this->existingItem($invoiceId, $itemId, '3', '200'), $this->newItem($invoiceId, '', '1', '10')]);

        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null, 'Body: ' . $response->body());
        self::assertArrayHasKey('item_name', $json['validation_errors'] ?? []);
        $this->assertItemUntouched($invoiceId, $itemId);
    }

    #[Test]
    public function it_rolls_back_the_items_when_the_invoice_number_is_invalid(): void
    {
        [$invoiceId, $itemId] = $this->invoiceWithOneItem();

        $response = $this->saveItems($invoiceId, [$this->existingItem($invoiceId, $itemId, '3', '200')], ['invoice_number' => 'bad<number>']);

        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null, 'Body: ' . $response->body());
        self::assertArrayHasKey('invoice_number', $json['validation_errors'] ?? []);
        $this->assertItemUntouched($invoiceId, $itemId);
    }

    #[Test]
    public function it_accepts_a_discount_that_brings_the_total_to_exactly_zero(): void
    {
        [$invoiceId, $itemId] = $this->invoiceWithOneItem();

        $response = $this->saveItems($invoiceId, [$this->existingItem($invoiceId, $itemId)], ['invoice_discount_amount' => '300']);

        self::assertSame(1, json_decode($response->body(), true)['success'] ?? null, 'Body: ' . $response->body());
        $this->assertInvoiceTotal($invoiceId, '0.00');
    }

    // ---- what must keep working ----------------------------------------------------------------

    #[Test]
    public function it_still_saves_zero_and_positive_amounts(): void
    {
        [$invoiceId, $itemId] = $this->invoiceWithOneItem();

        $response = $this->saveItems($invoiceId, [
            $this->existingItem($invoiceId, $itemId),
            $this->newItem($invoiceId, 'Free sample', '0', '0'),
            $this->newItem($invoiceId, 'Paid extra', '3', '10', '5'),
        ]);

        $json = json_decode($response->body(), true);
        self::assertSame(1, $json['success'] ?? null, 'Body: ' . $response->body());
        $this->assertDatabaseHas('ip_invoice_items', ['invoice_id' => $invoiceId, 'item_name' => 'Free sample']);
        $this->assertInvoiceTotal($invoiceId, '315.00'); // 300 + 3 x (10 - 5): the item discount is per unit
    }

    #[Test]
    public function it_writes_nothing_when_a_later_item_is_invalid(): void
    {
        /* Arrange: the first item is valid and changes the stored price, the second is not */
        [$invoiceId, $itemId] = $this->invoiceWithOneItem();
        $edited               = $this->existingItem($invoiceId, $itemId);
        $edited['item_price'] = '999';

        /* Act */
        $this->saveItems($invoiceId, [$edited, $this->newItem($invoiceId, 'Bad', '-1', '10')]);

        /* Assert: the valid edit before the bad line was not applied either */
        $this->assertDatabaseHas('ip_invoice_items', ['item_id' => $itemId, 'item_price' => '150.00']);
        $this->assertInvoiceTotal($invoiceId, '300.00');
    }

    // ---- items of other invoices ---------------------------------------------------------------

    #[Test]
    public function it_refuses_an_item_id_that_belongs_to_another_invoice(): void
    {
        [$invoiceA, $itemA]   = $this->invoiceWithOneItem();
        [$invoiceB, $itemB]   = $this->invoiceWithOneItem();
        $hijack               = $this->existingItem($invoiceA, $itemB);
        $hijack['item_price'] = '1';

        $response = $this->saveItems($invoiceA, [$this->existingItem($invoiceA, $itemA), $hijack]);

        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        self::assertSame('One of the submitted items does not belong to this document.', $json['validation_errors']['item_id'] ?? null);
        $this->assertDatabaseHas('ip_invoice_items', ['item_id' => $itemB, 'invoice_id' => $invoiceB, 'item_price' => '150.00']);
        $this->assertInvoiceTotal($invoiceB, '300.00');
    }

    #[Test]
    public function it_refuses_an_item_id_that_does_not_exist(): void
    {
        [$invoiceId, $itemId] = $this->invoiceWithOneItem();

        $response = $this->saveItems($invoiceId, [$this->existingItem($invoiceId, $itemId), $this->existingItem($invoiceId, 987654)]);

        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        self::assertArrayHasKey('item_id', $json['validation_errors']);
    }

    #[Test]
    public function it_saves_a_new_item_to_the_posted_invoice_even_if_the_item_names_another(): void
    {
        [$invoiceA] = $this->invoiceWithOneItem();
        [$invoiceB] = $this->invoiceWithOneItem();

        $response = $this->saveItems($invoiceA, [$this->newItem($invoiceB, 'Misdirected', '1', '10')]);

        $json = json_decode($response->body(), true);
        self::assertSame(1, $json['success'] ?? null, 'Body: ' . $response->body());
        $this->assertDatabaseHas('ip_invoice_items', ['invoice_id' => $invoiceA, 'item_name' => 'Misdirected']);
        $this->assertDatabaseMissing('ip_invoice_items', ['invoice_id' => $invoiceB, 'item_name' => 'Misdirected']);
    }

    // ---- credit invoices -----------------------------------------------------------------------

    #[Test]
    public function it_creates_a_credit_invoice_with_a_negative_total(): void
    {
        [$sourceId] = $this->invoiceWithOneItem();

        $response = $this->ajax('POST', '/invoices/ajax/create_credit', [
            'client_id'            => (string) $this->databaseFetchOne('ip_invoices', ['invoice_id' => $sourceId])['client_id'],
            'invoice_date_created' => date('Y-m-d'), 'invoice_time_created' => date('H:i:s'), 'invoice_group_id' => '1', 'user_id' => '1',
            'invoice_id'           => (string) $sourceId,
        ]);

        $json = json_decode($response->body(), true);
        self::assertSame(1, $json['success'] ?? null, 'Body: ' . $response->body());
        $credit = $this->databaseFetchOne('ip_invoice_amounts', ['invoice_id' => $json['invoice_id']]);
        self::assertSame('-300.00', $credit['invoice_total'], 'A credit invoice legitimately totals less than zero.');
        self::assertSame('-1', (string) $credit['invoice_sign']);

        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $json['invoice_id'], 'creditinvoice_parent_id' => $sourceId]);
        [$bystander] = $this->invoiceWithOneItem();
        self::assertSame(1, $this->databaseCount('ip_invoices', ['creditinvoice_parent_id' => $sourceId]), 'Only the new credit invoice points at its source.');
        self::assertSame(0, (int) $this->databaseFetchOne('ip_invoices', ['invoice_id' => $sourceId])['creditinvoice_parent_id']);
        self::assertSame(0, (int) $this->databaseFetchOne('ip_invoices', ['invoice_id' => $bystander])['creditinvoice_parent_id']);
    }

    #[Test]
    public function it_still_lets_a_credit_invoice_be_saved_with_its_negative_quantities(): void
    {
        $creditId = $this->creditInvoice();
        $item     = $this->databaseFetchOne('ip_invoice_items', ['invoice_id' => $creditId]);

        $response = $this->saveItems($creditId, [$this->existingItem($creditId, (int) $item['item_id'], '-2', '150')]);

        $json = json_decode($response->body(), true);
        self::assertSame(1, $json['success'] ?? null, 'Body: ' . $response->body());
        $this->assertInvoiceTotal($creditId, '-300.00');
    }

    #[Test]
    public function it_rejects_a_positive_quantity_or_a_negative_price_on_a_credit_invoice(): void
    {
        $creditId = $this->creditInvoice();
        $item     = $this->databaseFetchOne('ip_invoice_items', ['invoice_id' => $creditId]);

        $positive = json_decode($this->saveItems($creditId, [$this->existingItem($creditId, (int) $item['item_id'], '2', '150')])->body(), true);
        $negative = json_decode($this->saveItems($creditId, [$this->existingItem($creditId, (int) $item['item_id'], '-2', '-150')])->body(), true);

        self::assertSame('Quantities on a credit invoice must be zero or negative.', $positive['validation_errors']['item_quantity'] ?? null);
        self::assertSame('The item price cannot be negative.', $negative['validation_errors']['item_price'] ?? null);
        $this->assertInvoiceTotal($creditId, '-300.00');
    }

    #[Test]
    public function it_treats_a_legacy_credit_invoice_marked_only_by_its_sign_as_a_credit_invoice(): void
    {
        [$invoiceId, $itemId] = $this->invoiceWithOneItem();
        $this->databaseUpdate('ip_invoice_amounts', ['invoice_sign' => -1], ['invoice_id' => $invoiceId]);

        $response = $this->saveItems($invoiceId, [$this->existingItem($invoiceId, $itemId, '-2', '150')]);

        self::assertSame(1, json_decode($response->body(), true)['success'] ?? null, 'Body: ' . $response->body());
        $this->assertInvoiceTotal($invoiceId, '-300.00');
    }

    #[Test]
    public function it_treats_an_invoice_that_names_a_credited_parent_as_a_credit_invoice(): void
    {
        [$invoiceId, $itemId] = $this->invoiceWithOneItem();
        $this->databaseUpdate('ip_invoices', ['creditinvoice_parent_id' => 1], ['invoice_id' => $invoiceId]);

        $response = $this->saveItems($invoiceId, [$this->existingItem($invoiceId, $itemId, '-2', '150')]);

        self::assertSame(1, json_decode($response->body(), true)['success'] ?? null, 'Body: ' . $response->body());
        $this->assertInvoiceTotal($invoiceId, '-300.00');
    }

    #[Test]
    #[DataProvider('smallNonNegativeTotals')]
    public function it_stores_a_zero_or_smallest_positive_total_for_a_regular_invoice(string $price, string $expectedTotal): void
    {
        [$invoiceId, $itemId] = $this->invoiceWithOneItem();

        $response = $this->saveItems($invoiceId, [$this->existingItem($invoiceId, $itemId, '1', $price)]);

        self::assertSame(1, json_decode($response->body(), true)['success'] ?? null, 'Body: ' . $response->body());
        $this->assertInvoiceTotal($invoiceId, $expectedTotal);
    }

    // ---- defence in depth ----------------------------------------------------------------------

    #[Test]
    public function it_refuses_to_store_a_negative_total_for_a_regular_invoice_even_if_a_bad_row_got_in(): void
    {
        /* Arrange: a bad line inserted behind the controller's back (-10 x 50 = -500 against 300 + 10) */
        [$invoiceId, $itemId] = $this->invoiceWithOneItem();
        $deletable            = $this->databaseInsert('ip_invoice_items', [
            'invoice_id' => $invoiceId, 'item_name' => 'Small', 'item_description' => '', 'item_quantity' => '1', 'item_price' => '10',
            'item_order' => 2, 'item_date_added' => date('Y-m-d'),
        ]);
        $this->databaseInsert('ip_invoice_item_amounts', ['item_id' => $deletable, 'item_subtotal' => '10.00', 'item_tax_total' => '0.00', 'item_discount' => '0.00', 'item_total' => '10.00']);
        $bad = $this->databaseInsert('ip_invoice_items', [
            'invoice_id' => $invoiceId, 'item_name' => 'Smuggled', 'item_description' => '', 'item_quantity' => '-10', 'item_price' => '50',
            'item_order' => 3, 'item_date_added' => date('Y-m-d'),
        ]);
        $this->databaseInsert('ip_invoice_item_amounts', ['item_id' => $bad, 'item_subtotal' => '-500.00', 'item_tax_total' => '0.00', 'item_discount' => '0.00', 'item_total' => '-500.00']);
        $before = $this->databaseFetchOne('ip_invoice_amounts', ['invoice_id' => $invoiceId]);

        /* Act: any recalculation (deleting the small item) now sums to 300 - 500 = -200 */
        $this->ajax('POST', '/invoices/ajax/delete_item/' . $invoiceId, ['item_id' => (string) $deletable]);

        /* Assert */
        $after = $this->databaseFetchOne('ip_invoice_amounts', ['invoice_id' => $invoiceId]);
        self::assertSame($before['invoice_total'], $after['invoice_total'], 'The stored total is kept, never driven negative.');
        self::assertGreaterThanOrEqual(0, (float) $after['invoice_total']);
    }

    // ---- helpers -------------------------------------------------------------------------------

    /**
     * @return array{0: int, 1: int} [invoiceId, itemId] of a saved 300.00 invoice (2 x 150)
     */
    private function invoiceWithOneItem(): array
    {
        $invoiceId = $this->seedInvoice($this->seedClient(), ['invoice_number' => 'SIGN-' . bin2hex(random_bytes(3))]);
        $response  = $this->saveItems($invoiceId, [$this->newItem($invoiceId, 'Widget', '2', '150')]);
        self::assertSame(1, json_decode($response->body(), true)['success'] ?? null, 'Setup save failed: ' . $response->body());

        $itemId = (int) $this->databaseFetchOne('ip_invoice_items', ['invoice_id' => $invoiceId, 'item_name' => 'Widget'])['item_id'];
        $this->assertInvoiceTotal($invoiceId, '300.00');

        return [$invoiceId, $itemId];
    }

    private function creditInvoice(): int
    {
        [$sourceId] = $this->invoiceWithOneItem();
        $response   = $this->ajax('POST', '/invoices/ajax/create_credit', [
            'client_id'            => (string) $this->databaseFetchOne('ip_invoices', ['invoice_id' => $sourceId])['client_id'],
            'invoice_date_created' => date('Y-m-d'), 'invoice_time_created' => date('H:i:s'), 'invoice_group_id' => '1', 'user_id' => '1',
            'invoice_id'           => (string) $sourceId,
        ]);

        return (int) json_decode($response->body(), true)['invoice_id'];
    }

    /**
     * @param list<array<string, string>> $items
     * @param array<string, string>       $extra
     */
    private function saveItems(int $invoiceId, array $items, array $extra = [])
    {
        return $this->ajax('POST', '/invoices/ajax/save', array_merge([
            'invoice_id'               => (string) $invoiceId,
            'invoice_number'           => 'SIGN-' . $invoiceId,
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
    private function newItem(int $invoiceId, string $name, string $quantity, string $price, string $discount = ''): array
    {
        return [
            'invoice_id'   => (string) $invoiceId, 'item_id' => '', 'item_name' => $name, 'item_description' => '', 'item_quantity' => $quantity,
            'item_price'   => $price, 'item_discount_amount' => $discount, 'item_product_id' => '', 'item_product_unit_id' => '',
            'item_task_id' => '', 'item_tax_rate_id' => '0',
        ];
    }

    /** @return array<string, string> */
    private function existingItem(int $invoiceId, int $itemId, string $quantity = '2', string $price = '150'): array
    {
        return ['item_id' => (string) $itemId] + $this->newItem($invoiceId, 'Widget', $quantity, $price);
    }

    private function assertItemUntouched(int $invoiceId, int $itemId): void
    {
        $item = $this->databaseFetchOne('ip_invoice_items', ['item_id' => $itemId]);
        self::assertSame(150.0, (float) $item['item_price'], 'the item edit must be rolled back');
        self::assertSame(2.0, (float) $item['item_quantity']);
        $this->assertInvoiceTotal($invoiceId, '300.00');
    }

    private function assertInvoiceTotal(int $invoiceId, string $total): void
    {
        self::assertSame($total, $this->databaseFetchOne('ip_invoice_amounts', ['invoice_id' => $invoiceId])['invoice_total'] ?? null);
    }
}
