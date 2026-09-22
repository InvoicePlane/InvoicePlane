<?php

namespace Tests\Feature\Quotes;

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
#[CoversClass(\Ajax::class)]
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
