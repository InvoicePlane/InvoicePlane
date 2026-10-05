<?php

namespace Tests\Feature\Quotes;

use Ajax;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Quotes AJAX controller — application/modules/quotes/controllers/Ajax.php.
 *
 * Quote creation and editing happen here (quotes/ajax/create, quotes/ajax/save).
 * Required create fields (Mdl_Quotes::validation_rules): client_id,
 * quote_date_created, invoice_group_id. Absorbs the AJAX half of QuotesTest.
 */
#[Group('quotes')]
#[CoversClass(Ajax::class)]
class QuotesAjaxControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        // Mdl_quotes::get_date_expires() builds a DateInterval straight from this
        // setting with no fallback; a real install seeds it during setup.
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'quotes_expire_after', 'setting_value' => '15']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoices_due_after', 'setting_value' => '30']);
    }

    // -------------------------------------------------------------------------
    // Create — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_a_quote(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Ajax Quote Client']);

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/create', $this->createPayload($clientId));
        $data     = json_decode($response->body(), true);

        /* Assert */
        self::assertSame(1, $data['success'] ?? null, 'Create must report success: ' . $response->body());
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => (int) ($data['quote_id'] ?? 0), 'client_id' => $clientId]);
    }

    // -------------------------------------------------------------------------
    // Create — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_a_quote_without_client_id(): void
    {
        /* Arrange */
        $clientId             = $this->seedClient();
        $payload              = $this->createPayload($clientId);
        $payload['client_id'] = '';

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/create', $payload);
        $data     = json_decode($response->body(), true);

        /* Assert */
        self::assertNotSame(1, $data['success'] ?? null, 'A quote without a client_id must not be created.');
        $this->assertDatabaseCount('ip_quotes', 0);
    }

    #[Test]
    public function it_fails_to_create_a_quote_without_quote_date_created(): void
    {
        /* Arrange */
        $clientId                      = $this->seedClient();
        $payload                       = $this->createPayload($clientId);
        $payload['quote_date_created'] = '';

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/create', $payload);
        $data     = json_decode($response->body(), true);

        /* Assert */
        self::assertNotSame(1, $data['success'] ?? null, 'A quote without a creation date must not be created.');
        $this->assertDatabaseCount('ip_quotes', 0);
    }

    #[Test]
    public function it_fails_to_create_a_quote_without_invoice_group_id(): void
    {
        /* Arrange */
        $clientId                    = $this->seedClient();
        $payload                     = $this->createPayload($clientId);
        $payload['invoice_group_id'] = '';

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/create', $payload);
        $data     = json_decode($response->body(), true);

        /* Assert */
        self::assertNotSame(1, $data['success'] ?? null, 'A quote without an invoice group must not be created.');
        $this->assertDatabaseCount('ip_quotes', 0);
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_refuses_quote_creation_for_a_guest(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $this->actingAsGuest();

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/create', $this->createPayload($clientId));

        /* Assert */
        self::assertStringNotContainsString('"success":1', $response->body(), 'A guest must not create a quote.');
        $this->assertDatabaseCount('ip_quotes', 0);
    }

    /** @return array<string,string> */
    // -------------------------------------------------------------------------
    // save()
    // -------------------------------------------------------------------------

    #[Test]
    public function it_saves_a_quote_with_items_and_numbers_a_non_draft_quote(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $quoteId  = $this->seedQuote($clientId, ['quote_status_id' => 1, 'quote_number' => '']);
        $payload  = array_merge($this->savePayload($quoteId), [
            'quote_status_id' => '2',
            'quote_number'    => '',
            'notes'           => 'Net 30, pay by wire',
            'items'           => json_encode([$this->itemPayload($quoteId, 'Widget', '2', '50')]),
        ]);

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/save', $payload);

        /* Assert */
        $data = json_decode($response->body(), true);
        self::assertSame(1, $data['success'] ?? null, 'Body: ' . $response->body());
        $quote = $this->databaseFetchOne('ip_quotes', ['quote_id' => $quoteId]);
        self::assertSame('2', (string) $quote['quote_status_id']);
        self::assertSame('Net 30, pay by wire', $quote['notes']);
        self::assertNotSame('', (string) $quote['quote_number'], 'Leaving draft must allocate a quote number.');
        $this->assertDatabaseHas('ip_quote_items', ['quote_id' => $quoteId, 'item_name' => 'Widget', 'item_quantity' => '2.00']);
        $this->assertDatabaseHas('ip_quote_amounts', ['quote_id' => $quoteId, 'quote_item_subtotal' => '100.00']);
    }

    #[Test]
    public function it_keeps_a_draft_quote_unnumbered_when_saved_as_draft(): void
    {
        /* Arrange */
        $quoteId = $this->seedQuote($this->seedClient(), ['quote_status_id' => 1, 'quote_number' => '']);

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/save', array_merge($this->savePayload($quoteId), ['quote_status_id' => '1', 'quote_number' => '']));

        /* Assert */
        self::assertSame(1, json_decode($response->body(), true)['success'] ?? null);
        self::assertSame('', (string) $this->databaseFetchOne('ip_quotes', ['quote_id' => $quoteId])['quote_number']);
    }

    #[Test]
    public function it_rejects_a_save_without_an_expiry_date_and_changes_nothing(): void
    {
        /* Arrange */
        $quoteId = $this->seedQuote($this->seedClient(), ['quote_status_id' => 1, 'notes' => 'original notes']);
        $payload = array_merge($this->savePayload($quoteId), [
            'quote_date_expires' => '',
            'notes'              => 'must not be saved',
            'items'              => json_encode([$this->itemPayload($quoteId, 'Ghost item', '1', '10')]),
        ]);

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/save', $payload);

        /* Assert */
        self::assertSame(0, json_decode($response->body(), true)['success'] ?? null);
        self::assertSame('original notes', $this->databaseFetchOne('ip_quotes', ['quote_id' => $quoteId])['notes']);
        $this->assertDatabaseMissing('ip_quote_items', ['quote_id' => $quoteId]);
    }

    #[Test]
    public function it_rejects_a_save_with_a_duplicate_quote_number(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $this->seedQuote($clientId, ['quote_number' => 'QUO-TAKEN']);
        $quoteId = $this->seedQuote($clientId, ['quote_number' => 'QUO-MINE']);

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/save', array_merge($this->savePayload($quoteId), ['quote_number' => 'QUO-TAKEN']));

        /* Assert */
        self::assertSame(0, json_decode($response->body(), true)['success'] ?? null);
        self::assertSame('QUO-MINE', $this->databaseFetchOne('ip_quotes', ['quote_id' => $quoteId])['quote_number']);
    }

    #[Test]
    public function it_rejects_an_item_that_has_a_price_but_no_name(): void
    {
        /* Arrange */
        $quoteId = $this->seedQuote($this->seedClient());
        $payload = array_merge($this->savePayload($quoteId), [
            'notes' => 'SHOULD NOT PERSIST',
            'items' => json_encode([$this->itemPayload($quoteId, '', '1', '99')]),
        ]);

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/save', $payload);

        /* Assert: ONE parsable JSON document (a missing return once appended a second {"success":1}) and no save */
        $data = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame(0, $data['success'] ?? null);
        self::assertArrayHasKey('item_name', $data['validation_errors'] ?? []);
        $this->assertDatabaseMissing('ip_quote_items', ['quote_id' => $quoteId]);
        self::assertNotSame('SHOULD NOT PERSIST', $this->databaseFetchOne('ip_quotes', ['quote_id' => $quoteId])['notes'], 'A rejected save must not write the quote header.');
    }

    #[Test]
    public function it_applies_only_the_percent_discount_when_both_discounts_are_posted(): void
    {
        /* Arrange */
        $quoteId = $this->seedQuote($this->seedClient());
        $payload = array_merge($this->savePayload($quoteId), ['quote_discount_percent' => '10', 'quote_discount_amount' => '25']);

        /* Act */
        $this->ajax('POST', '/quotes/ajax/save', $payload);

        /* Assert: the two global discounts are mutually exclusive; percent wins */
        $quote = $this->databaseFetchOne('ip_quotes', ['quote_id' => $quoteId]);
        self::assertEquals(10, $quote['quote_discount_percent']);
        self::assertEquals(0, $quote['quote_discount_amount']);
    }

    // -------------------------------------------------------------------------
    // items
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_quote_item(): void
    {
        /* Arrange */
        $quoteId = $this->seedQuote($this->seedClient());
        $itemId  = $this->seedQuoteItem($quoteId, 'Disposable');
        $keepId  = $this->seedQuoteItem($quoteId, 'Keeper');

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/delete_item/' . $quoteId, ['item_id' => (string) $itemId]);

        /* Assert */
        self::assertSame(1, json_decode($response->body(), true)['success'] ?? null);
        $this->assertDatabaseMissing('ip_quote_items', ['item_id' => $itemId]);
        $this->assertDatabaseHas('ip_quote_items', ['item_id' => $keepId]);
    }

    #[Test]
    public function it_does_not_delete_an_item_when_the_quote_does_not_exist(): void
    {
        /* Arrange */
        $quoteId = $this->seedQuote($this->seedClient());
        $itemId  = $this->seedQuoteItem($quoteId, 'Protected');

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/delete_item/999999', ['item_id' => (string) $itemId]);

        /* Assert */
        self::assertSame(0, json_decode($response->body(), true)['success'] ?? null);
        $this->assertDatabaseHas('ip_quote_items', ['item_id' => $itemId]);
    }

    #[Test]
    public function it_returns_a_quote_item_by_id(): void
    {
        /* Arrange */
        $quoteId = $this->seedQuote($this->seedClient());
        $this->seedQuoteItem($quoteId, 'Other item');
        $itemId = $this->seedQuoteItem($quoteId, 'Lookup me');

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/get_item', ['item_id' => (string) $itemId]);

        /* Assert */
        $data = json_decode($response->body(), true);
        self::assertSame('Lookup me', $data['item_name'] ?? null);
        self::assertSame((string) $itemId, (string) $data['item_id']);
    }

    // -------------------------------------------------------------------------
    // quote tax rates
    // -------------------------------------------------------------------------

    #[Test]
    public function it_saves_a_quote_tax_rate(): void
    {
        /* Arrange */
        $quoteId   = $this->seedQuote($this->seedClient());
        $taxRateId = $this->databaseInsert('ip_tax_rates', ['tax_rate_name' => 'VAT 21', 'tax_rate_percent' => '21.00']);

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/save_quote_tax_rate', [
            'quote_id' => (string) $quoteId, 'tax_rate_id' => (string) $taxRateId, 'include_item_tax' => '0',
        ]);

        /* Assert */
        self::assertSame(1, json_decode($response->body(), true)['success'] ?? null, $response->body());
        $this->assertDatabaseHas('ip_quote_tax_rates', ['quote_id' => $quoteId, 'tax_rate_id' => $taxRateId]);
    }

    #[Test]
    public function it_rejects_a_quote_tax_rate_without_a_tax_rate(): void
    {
        /* Arrange */
        $quoteId = $this->seedQuote($this->seedClient());

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/save_quote_tax_rate', ['quote_id' => (string) $quoteId, 'include_item_tax' => '0']);

        /* Assert */
        self::assertSame(0, json_decode($response->body(), true)['success'] ?? null);
        $this->assertDatabaseMissing('ip_quote_tax_rates', ['quote_id' => $quoteId]);
    }

    // -------------------------------------------------------------------------
    // copy / reassign
    // -------------------------------------------------------------------------

    #[Test]
    public function it_copies_a_quote_with_its_items_and_leaves_the_original_intact(): void
    {
        /* Arrange */
        $sourceClient = $this->seedClient(['client_name' => 'Source Client']);
        $targetClient = $this->seedClient(['client_name' => 'Target Client']);
        $sourceId     = $this->seedQuote($sourceClient, ['quote_number' => 'QUO-SRC']);
        $this->seedQuoteItem($sourceId, 'Copied item');

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/copy_quote', array_merge($this->createPayload($targetClient), ['quote_id' => (string) $sourceId]));

        /* Assert */
        $data = json_decode($response->body(), true);
        self::assertSame(1, $data['success'] ?? null, $response->body());
        $copyId = (int) $data['quote_id'];
        self::assertNotSame($sourceId, $copyId);
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $copyId, 'client_id' => $targetClient]);
        $this->assertDatabaseHas('ip_quote_items', ['quote_id' => $copyId, 'item_name' => 'Copied item']);
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $sourceId, 'client_id' => $sourceClient, 'quote_number' => 'QUO-SRC']);
        $this->assertDatabaseHas('ip_quote_items', ['quote_id' => $sourceId, 'item_name' => 'Copied item']);
    }

    #[Test]
    public function it_fails_to_copy_a_quote_without_a_client(): void
    {
        /* Arrange */
        $sourceId = $this->seedQuote($this->seedClient());

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/copy_quote', array_merge($this->createPayload(0), ['client_id' => '', 'quote_id' => (string) $sourceId]));

        /* Assert */
        self::assertSame(0, json_decode($response->body(), true)['success'] ?? null);
        $this->assertDatabaseCount('ip_quotes', 1);
    }

    #[Test]
    public function it_changes_the_quotes_user(): void
    {
        /* Arrange */
        $quoteId = $this->seedQuote($this->seedClient());
        $userId  = $this->databaseInsert('ip_users', $this->userRow('new-owner@test.local'));

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/change_user', ['quote_id' => (string) $quoteId, 'user_id' => (string) $userId]);

        /* Assert */
        self::assertSame(1, json_decode($response->body(), true)['success'] ?? null);
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $quoteId, 'user_id' => $userId]);
    }

    #[Test]
    public function it_refuses_to_change_the_quotes_user_to_an_unknown_user(): void
    {
        /* Arrange */
        $quoteId = $this->seedQuote($this->seedClient());

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/change_user', ['quote_id' => (string) $quoteId, 'user_id' => '999999']);

        /* Assert */
        self::assertSame(0, json_decode($response->body(), true)['success'] ?? null);
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $quoteId, 'user_id' => 1]);
    }

    #[Test]
    public function it_changes_the_quotes_client(): void
    {
        /* Arrange */
        $oldClient = $this->seedClient(['client_name' => 'Old Client']);
        $newClient = $this->seedClient(['client_name' => 'New Client']);
        $quoteId   = $this->seedQuote($oldClient);

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/change_client', ['quote_id' => (string) $quoteId, 'client_id' => (string) $newClient]);

        /* Assert */
        self::assertSame(1, json_decode($response->body(), true)['success'] ?? null);
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $quoteId, 'client_id' => $newClient]);
    }

    #[Test]
    public function it_refuses_to_change_the_quotes_client_to_an_unknown_client(): void
    {
        /* Arrange */
        $client  = $this->seedClient();
        $quoteId = $this->seedQuote($client);

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/change_client', ['quote_id' => (string) $quoteId, 'client_id' => '999999']);

        /* Assert */
        self::assertSame(0, json_decode($response->body(), true)['success'] ?? null);
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $quoteId, 'client_id' => $client]);
    }

    // -------------------------------------------------------------------------
    // quote -> invoice
    // -------------------------------------------------------------------------

    #[Test]
    public function it_converts_a_quote_into_an_invoice_carrying_items_and_discount(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $quoteId  = $this->seedQuote($clientId, ['quote_discount_percent' => '10.00']);
        $this->seedQuoteItem($quoteId, 'Consulting', '3', '100');

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/quote_to_invoice', [
            'quote_id' => (string) $quoteId, 'client_id' => (string) $clientId, 'invoice_date_created' => date('Y-m-d'),
            'invoice_time_created' => date('H:i:s'), 'invoice_group_id' => '1', 'user_id' => '1',
        ]);

        /* Assert */
        $data = json_decode($response->body(), true);
        self::assertSame(1, $data['success'] ?? null, $response->body());
        $invoiceId = (int) $data['invoice_id'];
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'client_id' => $clientId, 'invoice_discount_percent' => '10.00']);
        $this->assertDatabaseHas('ip_invoice_items', ['invoice_id' => $invoiceId, 'item_name' => 'Consulting', 'item_quantity' => '3.00']);
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $quoteId, 'invoice_id' => $invoiceId]);
        $this->assertDatabaseHas('ip_invoice_amounts', ['invoice_id' => $invoiceId, 'invoice_item_subtotal' => '300.00']);
    }

    #[Test]
    public function it_creates_no_invoice_when_the_conversion_form_is_incomplete(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $quoteId  = $this->seedQuote($clientId);
        $this->seedQuoteItem($quoteId, 'Untouched');

        /* Act: no invoice_group_id */
        $response = $this->ajax('POST', '/quotes/ajax/quote_to_invoice', [
            'quote_id' => (string) $quoteId, 'client_id' => (string) $clientId, 'invoice_date_created' => date('Y-m-d'),
            'invoice_time_created' => date('H:i:s'), 'user_id' => '1',
        ]);

        /* Assert */
        self::assertSame(0, json_decode($response->body(), true)['success'] ?? null);
        $this->assertDatabaseCount('ip_invoices', 0);
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $quoteId, 'invoice_id' => 0]);
    }

    #[Test]
    public function it_reports_an_unknown_quote_in_the_conversion_modal(): void
    {
        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/modal_quote_to_invoice/999999');

        /* Assert */
        self::assertSame(0, json_decode($response->body(), true)['success'] ?? null);
    }

    #[Test]
    public function it_renders_the_conversion_modal_for_an_existing_quote(): void
    {
        /* Arrange */
        $quoteId = $this->seedQuote($this->seedClient(), ['quote_number' => 'QUO-MODAL-1']);

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/modal_quote_to_invoice/' . $quoteId);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'quote_id');
        $this->assertResponseHasNoPhpErrors($response);
    }

    // -------------------------------------------------------------------------
    // helpers
    // -------------------------------------------------------------------------

    /** @param array<string,mixed> $overrides */
    private function seedQuote(int $clientId, array $overrides = []): int
    {
        $quoteId = $this->databaseInsert('ip_quotes', array_merge([
            'user_id' => 1, 'client_id' => $clientId, 'invoice_group_id' => 1, 'quote_status_id' => 2,
            'quote_number' => 'QUO-' . bin2hex(random_bytes(3)), 'quote_url_key' => bin2hex(random_bytes(16)),
            'quote_date_created' => date('Y-m-d'), 'quote_date_modified' => date('Y-m-d H:i:s'),
            'quote_date_expires' => date('Y-m-d', strtotime('+15 days')),
        ], $overrides));
        $this->databaseInsert('ip_quote_amounts', [
            'quote_id' => $quoteId, 'quote_item_subtotal' => '0.00', 'quote_item_tax_total' => '0.00',
            'quote_tax_total' => '0.00', 'quote_total' => '0.00',
        ]);

        return $quoteId;
    }

    private function seedQuoteItem(int $quoteId, string $name, string $quantity = '1', string $price = '10'): int
    {
        return $this->databaseInsert('ip_quote_items', [
            'quote_id' => $quoteId, 'item_name' => $name, 'item_description' => '', 'item_quantity' => $quantity,
            'item_price' => $price, 'item_order' => 1, 'item_date_added' => date('Y-m-d'),
        ]);
    }

    /** @return array<string,string> */
    private function itemPayload(int $quoteId, string $name, string $quantity, string $price): array
    {
        return [
            'quote_id' => (string) $quoteId, 'item_id' => '', 'item_name' => $name, 'item_description' => '',
            'item_quantity' => $quantity, 'item_price' => $price, 'item_discount_amount' => '', 'item_product_id' => '',
            'item_product_unit_id' => '', 'item_tax_rate_id' => '0',
        ];
    }

    /** @return array<string,string> */
    private function savePayload(int $quoteId): array
    {
        return [
            'quote_id' => (string) $quoteId, 'quote_status_id' => '2', 'quote_number' => 'QUO-SAVED-' . $quoteId,
            'quote_date_created' => date('Y-m-d'), 'quote_date_expires' => date('Y-m-d', strtotime('+15 days')),
            'quote_password' => '', 'notes' => '', 'quote_discount_percent' => '0', 'quote_discount_amount' => '0',
            'service_id' => '', 'items' => '[]',
        ];
    }

    /** @return array<string,mixed> */
    private function userRow(string $email): array
    {
        return [
            'user_type' => 1, 'user_name' => 'User ' . $email, 'user_email' => $email,
            'user_password' => password_hash('secret123', PASSWORD_DEFAULT), 'user_psalt' => bin2hex(random_bytes(8)),
            'user_language' => 'system', 'user_active' => 1,
            'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),
        ];
    }

    private function createPayload(int $clientId): array
    {
        return [
            'client_id'          => (string) $clientId,
            'quote_date_created' => date('Y-m-d'),
            'invoice_group_id'   => '1',
            'user_id'            => '1',
        ];
    }
}
