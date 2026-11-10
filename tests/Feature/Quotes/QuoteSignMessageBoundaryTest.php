<?php

namespace Tests\Feature\Quotes;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * GHSA-jvcw-w8pr-j92c: the quote save passes $credit_document=false and the 'quote' prefix to amount_sign_errors().
 * QuoteItemAmountSignTest pins the field names; this pins the messages and the single-field global discount cases.
 */
#[Group('quotes')]
#[Group('security')]
final class QuoteSignMessageBoundaryTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    /** @return array<string, array{array<string, string>, string, string}> */
    public static function singleDiscountFields(): array
    {
        return [
            'negative amount'  => [['quote_discount_amount' => '-1'], 'quote_discount_amount', 'The discount cannot be negative.'],
            'negative percent' => [['quote_discount_percent' => '-1'], 'quote_discount_percent', 'The discount cannot be negative.'],
            'percent over 100' => [['quote_discount_percent' => '100.5'], 'quote_discount_percent', 'The discount percentage cannot exceed 100.'],
        ];
    }

    #[Test]
    #[DataProvider('singleDiscountFields')]
    public function it_names_exactly_one_field_and_message_for_each_bad_global_discount(array $extra, string $field, string $message): void
    {
        /* Arrange */
        [$quoteId, $itemId] = $this->quoteWithOneItem();

        /* Act */
        $response = $this->saveItems($quoteId, [$this->existingItem($quoteId, $itemId, '2', '150')], $extra);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        self::assertSame([$field => $message], $json['validation_errors']);
        $this->assertDatabaseHas('ip_quote_amounts', ['quote_id' => $quoteId, 'quote_total' => '300.00']);
    }

    #[Test]
    public function it_uses_the_plain_negative_quantity_message_on_a_quote_and_accepts_zero(): void
    {
        /* Arrange */
        [$quoteId, $itemId] = $this->quoteWithOneItem();

        /* Act */
        $negative = json_decode($this->saveItems($quoteId, [$this->existingItem($quoteId, $itemId, '-2', '150')])->body(), true);
        $zero     = json_decode($this->saveItems($quoteId, [$this->existingItem($quoteId, $itemId, '0', '150')])->body(), true);

        /* Assert */
        self::assertSame(['item_quantity' => 'The item quantity cannot be negative.'], $negative['validation_errors']);
        self::assertSame(1, $zero['success'] ?? null, 'positive control: zero quantity is saved');
        $this->assertDatabaseHas('ip_quote_amounts', ['quote_id' => $quoteId, 'quote_total' => '0.00']);
    }

    #[Test]
    public function it_checks_a_negative_row_that_follows_a_valid_new_row(): void
    {
        /* Arrange */
        [$quoteId, $itemId] = $this->quoteWithOneItem();

        /* Act */
        $response = $this->saveItems($quoteId, [
            $this->existingItem($quoteId, $itemId, '2', '150'),
            $this->newItem($quoteId, 'Fine', '1', '10'),
            $this->newItem($quoteId, 'Bad', '1', '-10'),
        ]);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(['item_price' => 'The item price cannot be negative.'], $json['validation_errors'] ?? null);
        $this->assertDatabaseMissing('ip_quote_items', ['quote_id' => $quoteId, 'item_name' => 'Fine']);
        $this->assertDatabaseHas('ip_quote_amounts', ['quote_id' => $quoteId, 'quote_total' => '300.00']);
    }

    // ---- helpers -------------------------------------------------------------------------------

    /** @return array{0: int, 1: int} */
    private function quoteWithOneItem(): array
    {
        $quoteId = $this->databaseInsert('ip_quotes', [
            'user_id'            => 1, 'client_id' => $this->seedClient(), 'invoice_group_id' => 1, 'quote_status_id' => 2,
            'quote_number'       => 'QSMB-' . bin2hex(random_bytes(3)), 'quote_url_key' => bin2hex(random_bytes(16)),
            'quote_date_created' => date('Y-m-d'), 'quote_date_modified' => date('Y-m-d H:i:s'),
            'quote_date_expires' => date('Y-m-d', strtotime('+15 days')),
        ]);
        $this->databaseInsert('ip_quote_amounts', [
            'quote_id' => $quoteId, 'quote_item_subtotal' => '0.00', 'quote_item_tax_total' => '0.00', 'quote_tax_total' => '0.00', 'quote_total' => '0.00',
        ]);
        $response = $this->saveItems($quoteId, [$this->newItem($quoteId, 'Widget', '2', '150')]);
        self::assertSame(1, json_decode($response->body(), true)['success'] ?? null, 'Setup save failed: ' . $response->body());

        return [$quoteId, (int) $this->databaseFetchOne('ip_quote_items', ['quote_id' => $quoteId, 'item_name' => 'Widget'])['item_id']];
    }

    /**
     * @param list<array<string, string>> $items
     * @param array<string, string>       $extra
     */
    private function saveItems(int $quoteId, array $items, array $extra = [])
    {
        return $this->ajax('POST', '/quotes/ajax/save', array_merge([
            'quote_id'           => (string) $quoteId, 'quote_status_id' => '2', 'quote_number' => 'QSMB-' . $quoteId,
            'quote_date_created' => date('Y-m-d'), 'quote_date_expires' => date('Y-m-d', strtotime('+15 days')),
            'quote_password'     => '', 'notes' => '', 'quote_discount_percent' => '0', 'quote_discount_amount' => '0',
            'service_id'         => '', 'items' => json_encode($items),
        ], $extra));
    }

    /** @return array<string, string> */
    private function newItem(int $quoteId, string $name, string $quantity, string $price): array
    {
        return [
            'quote_id'   => (string) $quoteId, 'item_id' => '', 'item_name' => $name, 'item_description' => '', 'item_quantity' => $quantity,
            'item_price' => $price, 'item_discount_amount' => '', 'item_product_id' => '', 'item_product_unit_id' => '', 'item_tax_rate_id' => '0',
        ];
    }

    /** @return array<string, string> */
    private function existingItem(int $quoteId, int $itemId, string $quantity, string $price): array
    {
        return ['item_id' => (string) $itemId] + $this->newItem($quoteId, 'Widget', $quantity, $price);
    }
}
