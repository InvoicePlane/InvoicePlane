<?php

namespace Tests\Feature\Quotes;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Quotes share the invoice editor's item-saving code, and a quote is converted into an invoice, so
 * a negative quantity, price or discount must be refused here as well, with the stored quote left
 * untouched.
 */
#[Group('quotes')]
#[Group('security')]
final class QuoteItemAmountSignTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    /** @return array<string, array{string, string, string, string}> */
    public static function negativeAmounts(): array
    {
        return [
            'negative quantity' => ['-3', '150', '', 'item_quantity'],
            'negative price'    => ['3', '-150', '', 'item_price'],
            'negative discount' => ['1', '10', '-5', 'item_discount_amount'],
        ];
    }

    #[Test]
    #[DataProvider('negativeAmounts')]
    public function it_rejects_each_negative_amount_and_keeps_the_quote_unchanged(string $quantity, string $price, string $discount, string $field): void
    {
        [$quoteId, $itemId] = $this->quoteWithOneItem();

        $response = $this->saveItems($quoteId, [$this->existingItem($quoteId, $itemId), $this->newItem($quoteId, 'Extra', $quantity, $price, $discount)]);

        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        self::assertSame([$field], array_keys($json['validation_errors']));
        $this->assertDatabaseMissing('ip_quote_items', ['quote_id' => $quoteId, 'item_name' => 'Extra']);
        $this->assertDatabaseHas('ip_quote_amounts', ['quote_id' => $quoteId, 'quote_total' => '300.00']);
    }

    #[Test]
    public function it_rejects_the_double_negative_and_a_negative_global_discount(): void
    {
        [$quoteId, $itemId] = $this->quoteWithOneItem();

        $response = $this->saveItems($quoteId, [$this->existingItem($quoteId, $itemId), $this->newItem($quoteId, 'Adjustment', '-3', '-150')], ['quote_discount_amount' => '-10', 'quote_discount_percent' => '-5']);

        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        self::assertEqualsCanonicalizing(['item_quantity', 'item_price', 'quote_discount_amount', 'quote_discount_percent'], array_keys($json['validation_errors']));
        $this->assertDatabaseHas('ip_quote_amounts', ['quote_id' => $quoteId, 'quote_total' => '300.00']);
    }

    #[Test]
    public function it_rejects_a_discount_percent_above_one_hundred_and_keeps_the_quote_unchanged(): void
    {
        [$quoteId, $itemId] = $this->quoteWithOneItem();

        $response = $this->saveItems($quoteId, [$this->existingItem($quoteId, $itemId)], ['quote_discount_percent' => '101']);

        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        self::assertSame('The discount percentage cannot exceed 100.', $json['validation_errors']['quote_discount_percent'] ?? null);
        $this->assertDatabaseHas('ip_quote_amounts', ['quote_id' => $quoteId, 'quote_total' => '300.00']);
    }

    #[Test]
    public function it_accepts_a_discount_percent_of_exactly_one_hundred(): void
    {
        [$quoteId, $itemId] = $this->quoteWithOneItem();

        $response = $this->saveItems($quoteId, [$this->existingItem($quoteId, $itemId)], ['quote_discount_percent' => '100']);

        self::assertSame(1, json_decode($response->body(), true)['success'] ?? null, 'Body: ' . $response->body());
    }

    #[Test]
    public function it_still_saves_zero_and_positive_amounts(): void
    {
        [$quoteId, $itemId] = $this->quoteWithOneItem();

        $response = $this->saveItems($quoteId, [$this->existingItem($quoteId, $itemId), $this->newItem($quoteId, 'Free sample', '0', '0'), $this->newItem($quoteId, 'Extra', '2', '10')]);

        $json = json_decode($response->body(), true);
        self::assertSame(1, $json['success'] ?? null, 'Body: ' . $response->body());
        $this->assertDatabaseHas('ip_quote_amounts', ['quote_id' => $quoteId, 'quote_total' => '320.00']);
    }

    #[Test]
    public function it_refuses_an_item_id_of_another_quote_or_a_missing_one(): void
    {
        [$quoteA, $itemA]     = $this->quoteWithOneItem();
        [$quoteB, $itemB]     = $this->quoteWithOneItem();
        $hijack               = $this->existingItem($quoteA, $itemB);
        $hijack['item_price'] = '1';

        $foreign = json_decode($this->saveItems($quoteA, [$this->existingItem($quoteA, $itemA), $hijack])->body(), true);
        $missing = json_decode($this->saveItems($quoteA, [$this->existingItem($quoteA, 987654)])->body(), true);

        self::assertSame('One of the submitted items does not belong to this document.', $foreign['validation_errors']['item_id'] ?? null);
        self::assertArrayHasKey('item_id', $missing['validation_errors']);
        $this->assertDatabaseHas('ip_quote_items', ['item_id' => $itemB, 'quote_id' => $quoteB, 'item_price' => '150.00']);
    }

    #[Test]
    public function it_saves_a_new_item_to_the_posted_quote_even_if_the_item_names_another(): void
    {
        [$quoteA] = $this->quoteWithOneItem();
        [$quoteB] = $this->quoteWithOneItem();

        $response = $this->saveItems($quoteA, [$this->newItem($quoteB, 'Misdirected', '1', '10')]);

        self::assertSame(1, json_decode($response->body(), true)['success'] ?? null, 'Body: ' . $response->body());
        $this->assertDatabaseHas('ip_quote_items', ['quote_id' => $quoteA, 'item_name' => 'Misdirected']);
        $this->assertDatabaseMissing('ip_quote_items', ['quote_id' => $quoteB, 'item_name' => 'Misdirected']);
    }

    // ---- helpers -------------------------------------------------------------------------------

    /**
     * @return array{0: int, 1: int} [quoteId, itemId] of a saved 300.00 quote (2 x 150)
     */
    private function quoteWithOneItem(): array
    {
        $quoteId = $this->databaseInsert('ip_quotes', [
            'user_id'            => 1, 'client_id' => $this->seedClient(), 'invoice_group_id' => 1, 'quote_status_id' => 2,
            'quote_number'       => 'QSIGN-' . bin2hex(random_bytes(3)), 'quote_url_key' => bin2hex(random_bytes(16)),
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
            'quote_id'           => (string) $quoteId, 'quote_status_id' => '2', 'quote_number' => 'QSIGN-' . $quoteId,
            'quote_date_created' => date('Y-m-d'), 'quote_date_expires' => date('Y-m-d', strtotime('+15 days')),
            'quote_password'     => '', 'notes' => '', 'quote_discount_percent' => '0', 'quote_discount_amount' => '0',
            'service_id'         => '', 'items' => json_encode($items),
        ], $extra));
    }

    /** @return array<string, string> */
    private function newItem(int $quoteId, string $name, string $quantity, string $price, string $discount = ''): array
    {
        return [
            'quote_id'   => (string) $quoteId, 'item_id' => '', 'item_name' => $name, 'item_description' => '', 'item_quantity' => $quantity,
            'item_price' => $price, 'item_discount_amount' => $discount, 'item_product_id' => '', 'item_product_unit_id' => '', 'item_tax_rate_id' => '0',
        ];
    }

    /** @return array<string, string> */
    private function existingItem(int $quoteId, int $itemId): array
    {
        return ['item_id' => (string) $itemId] + $this->newItem($quoteId, 'Widget', '2', '150');
    }
}
