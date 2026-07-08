<?php

namespace Tests\Feature\Quotes;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class QuotesAjaxControllerTest extends AbstractTestCase
{
    private int $clientId;

    private int $invoiceGroupId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->clientId       = $this->seedClient(['client_name' => 'Ajax Quote Client']);
        $this->invoiceGroupId = $this->databaseInsert('ip_invoice_groups', [
            'invoice_group_name'              => 'Ajax Quote Group',
            'invoice_group_next_id'           => 1,
            'invoice_group_identifier_format' => 'QUO-{number}',
            'invoice_group_left_pad'          => 0,
        ]);
        // Ensure quotes_expire_after is set so DateInterval('P{n}D') is valid.
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'quotes_expire_after', 'setting_value' => '30']);
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_a_quote(): void
    {
        /**
         * POST /quotes/ajax/create
         * {
         *     "client_id": "<clientId>",
         *     "quote_date_created": "2026-06-21",
         *     "invoice_group_id": "<invoiceGroupId>"
         * }.
         */

        /* Arrange */

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/create', [
            'client_id'          => (string) $this->clientId,
            'quote_date_created' => '2026-06-21',
            'invoice_group_id'   => (string) $this->invoiceGroupId,
            'user_id'            => '1',
        ]);

        /* Assert */
        $body = $response->body();
        $json = json_decode($body, true);
        self::assertSame(1, $json['success'] ?? null, 'Ajax create must return success=1. Body: ' . $body);
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $json['quote_id']]);
    }

    #[Test]
    public function it_fails_to_create_a_quote_without_client_id(): void
    {
        /**
         * POST /quotes/ajax/create
         * {
         *     "client_id": "",
         *     "quote_date_created": "2026-06-21",
         *     "invoice_group_id": "<invoiceGroupId>"
         * }.
         */

        /* Arrange */

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/create', [
            'client_id'          => '',
            'quote_date_created' => '2026-06-21',
            'invoice_group_id'   => (string) $this->invoiceGroupId,
        ]);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null, 'Missing client_id must return success=0.');
    }

    #[Test]
    public function it_fails_to_create_a_quote_without_quote_date(): void
    {
        /**
         * POST /quotes/ajax/create
         * {
         *     "client_id": "<clientId>",
         *     "quote_date_created": "",
         *     "invoice_group_id": "<invoiceGroupId>"
         * }.
         */

        /* Arrange */

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/create', [
            'client_id'          => (string) $this->clientId,
            'quote_date_created' => '',
            'invoice_group_id'   => (string) $this->invoiceGroupId,
        ]);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null, 'Missing quote_date_created must return success=0.');
    }

    #[Test]
    public function it_fails_to_create_a_quote_without_invoice_group(): void
    {
        /**
         * POST /quotes/ajax/create
         * {
         *     "client_id": "<clientId>",
         *     "quote_date_created": "2026-06-21",
         *     "invoice_group_id": ""
         * }.
         */

        /* Arrange */

        /* Act */
        $response = $this->ajax('POST', '/quotes/ajax/create', [
            'client_id'          => (string) $this->clientId,
            'quote_date_created' => '2026-06-21',
            'invoice_group_id'   => '',
        ]);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null, 'Missing invoice_group_id must return success=0.');
    }

    // -------------------------------------------------------------------------
    // Guest redirect — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/quotes/status/all'); // Regular (non-Ajax) route for redirect check

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
    }
}
