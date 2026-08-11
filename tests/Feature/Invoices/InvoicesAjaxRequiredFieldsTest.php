<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * invoices/controllers/Ajax.php — the invoice editor's Ajax backend.
 * Focuses on required-field validation (each required field removed, one at
 * a time, asserting failure and no mutation) for the mutating actions, plus
 * happy paths and IDOR-adjacent edge cases.
 */
class InvoicesAjaxRequiredFieldsTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        // A real install seeds this during the setup wizard (Mdl_setup::$default_settings);
        // Mdl_invoices::get_date_due() builds a DateInterval directly from it with no
        // fallback, so create()/copy_invoice()/create_credit() all need it present.
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoices_due_after', 'setting_value' => '30']);
    }

    #[Test]
    public function it_creates_an_invoice_with_all_required_fields(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/create', $this->validCreatePayload($clientId));

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(1, $json['success'] ?? null, 'Body: ' . $response->body());
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $json['invoice_id'], 'client_id' => $clientId]);
    }

    #[Test]
    public function it_fails_to_create_an_invoice_without_client_id(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $payload  = $this->validCreatePayload($clientId);
        unset($payload['client_id']);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/create', $payload);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        $this->assertDatabaseCount('ip_invoices', 0);
    }

    #[Test]
    public function it_fails_to_create_an_invoice_without_invoice_date_created(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $payload  = $this->validCreatePayload($clientId);
        unset($payload['invoice_date_created']);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/create', $payload);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        $this->assertDatabaseCount('ip_invoices', 0);
    }

    #[Test]
    public function it_fails_to_create_an_invoice_without_invoice_group_id(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $payload  = $this->validCreatePayload($clientId);
        unset($payload['invoice_group_id']);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/create', $payload);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        $this->assertDatabaseCount('ip_invoices', 0);
    }

    #[Test]
    public function it_fails_to_create_an_invoice_without_invoice_time_created(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $payload  = $this->validCreatePayload($clientId);
        unset($payload['invoice_time_created']);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/create', $payload);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        $this->assertDatabaseCount('ip_invoices', 0);
    }

    #[Test]
    public function it_fails_to_create_an_invoice_without_user_id(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $payload  = $this->validCreatePayload($clientId);
        unset($payload['user_id']);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/create', $payload);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        $this->assertDatabaseCount('ip_invoices', 0);
    }

    #[Test]
    public function it_saves_an_invoice_with_all_required_fields(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId, ['invoice_number' => 'SAVE-REQ-001']);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/save', $this->validSavePayload($invoiceId));

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(1, $json['success'] ?? null, 'Body: ' . $response->body());
    }

    #[Test]
    public function it_fails_to_save_an_invoice_without_invoice_date_due(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId, ['invoice_number' => 'SAVE-REQ-002', 'invoice_date_due' => date('Y-m-d', strtotime('+10 days'))]);
        $payload   = $this->validSavePayload($invoiceId);
        unset($payload['invoice_date_due']);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/save', $payload);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'invoice_date_due' => date('Y-m-d', strtotime('+10 days'))]);
    }

    #[Test]
    public function it_fails_to_save_an_invoice_without_invoice_date_created(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId, ['invoice_number' => 'SAVE-REQ-003']);
        $payload   = $this->validSavePayload($invoiceId);
        unset($payload['invoice_date_created']);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/save', $payload);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
    }

    #[Test]
    public function it_rejects_an_invoice_number_with_unsafe_characters(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId, ['invoice_number' => 'SAVE-REQ-004']);
        $payload   = $this->validSavePayload($invoiceId);

        $payload['invoice_number'] = 'INV<script>alert(1)</script>';

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/save', $payload);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'invoice_number' => 'SAVE-REQ-004']);
    }

    // -------------------------------------------------------------------------
    // change_user() / change_client()
    // -------------------------------------------------------------------------

    #[Test]
    public function it_changes_the_invoices_user(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);
        $newUserId = $this->databaseInsert('ip_users', [
            'user_name'     => 'New Owner', 'user_email' => 'new-owner@test.local',
            'user_password' => password_hash('x', PASSWORD_DEFAULT), 'user_psalt' => bin2hex(random_bytes(10)),
            'user_type'     => 1, 'user_active' => 1, 'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),
        ]);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/change_user', ['user_id' => (string) $newUserId, 'invoice_id' => (string) $invoiceId]);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(1, $json['success'] ?? null);
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'user_id' => $newUserId]);
    }

    #[Test]
    public function it_fails_to_change_the_invoices_user_for_an_unknown_user_id(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId, ['user_id' => 1]);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/change_user', ['user_id' => '999999', 'invoice_id' => (string) $invoiceId]);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'user_id' => 1]);
    }

    #[Test]
    public function it_changes_the_invoices_client(): void
    {
        /* Arrange */
        $clientId    = $this->seedClient();
        $invoiceId   = $this->seedInvoice($clientId);
        $newClientId = $this->seedClient(['client_name' => 'New Owner Client']);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/change_client', ['client_id' => (string) $newClientId, 'invoice_id' => (string) $invoiceId]);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(1, $json['success'] ?? null);
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'client_id' => $newClientId]);
    }

    #[Test]
    public function it_fails_to_change_the_invoices_client_for_an_unknown_client_id(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/change_client', ['client_id' => '999999', 'invoice_id' => (string) $invoiceId]);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'client_id' => $clientId]);
    }

    // -------------------------------------------------------------------------
    // delete_item()
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_an_existing_invoice_item(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);
        $itemId    = $this->databaseInsert('ip_invoice_items', [
            'invoice_id' => $invoiceId, 'item_tax_rate_id' => 0, 'item_date_added' => date('Y-m-d'),
            'item_name'  => 'Deletable', 'item_description' => '', 'item_quantity' => '1.00', 'item_price' => '10.00', 'item_order' => 0,
        ]);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/delete_item/' . $invoiceId, ['item_id' => (string) $itemId]);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(1, $json['success'] ?? null);
        $this->assertDatabaseMissing('ip_invoice_items', ['item_id' => $itemId]);
    }

    #[Test]
    public function it_does_not_delete_anything_for_a_nonexistent_item_id(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);
        $itemId    = $this->databaseInsert('ip_invoice_items', [
            'invoice_id' => $invoiceId, 'item_tax_rate_id' => 0, 'item_date_added' => date('Y-m-d'),
            'item_name'  => 'Untouched', 'item_description' => '', 'item_quantity' => '1.00', 'item_price' => '10.00', 'item_order' => 0,
        ]);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/delete_item/' . $invoiceId, ['item_id' => '999999']);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        $this->assertDatabaseHas('ip_invoice_items', ['item_id' => $itemId]);
    }

    // -------------------------------------------------------------------------
    // save_invoice_tax_rate() — required: invoice_id, tax_rate_id, include_item_tax
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_save_an_invoice_tax_rate_without_invoice_id(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/save_invoice_tax_rate', ['tax_rate_id' => '1', 'include_item_tax' => '0']);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        $this->assertDatabaseCount('ip_invoice_tax_rates', 0);
    }

    // -------------------------------------------------------------------------
    // create_recurring() — required: invoice_id, recur_start_date, recur_frequency
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_a_recurring_invoice_with_all_required_fields(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/create_recurring', [
            'invoice_id'       => (string) $invoiceId,
            'recur_start_date' => date('Y-m-d'),
            'recur_frequency'  => '1D',
        ]);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(1, $json['success'] ?? null, 'Body: ' . $response->body());
        $this->assertDatabaseHas('ip_invoices_recurring', ['invoice_id' => $invoiceId]);
    }

    #[Test]
    public function it_fails_to_create_a_recurring_invoice_without_recur_start_date(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/create_recurring', [
            'invoice_id'      => (string) $invoiceId,
            'recur_frequency' => '1D',
        ]);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        $this->assertDatabaseCount('ip_invoices_recurring', 0);
    }

    // -------------------------------------------------------------------------
    // copy_invoice() / create_credit() (use the default validation_rules())
    // -------------------------------------------------------------------------

    #[Test]
    public function it_copies_an_invoice(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $sourceId = $this->seedInvoice($clientId, ['invoice_number' => 'COPY-SRC-001']);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/copy_invoice', array_merge($this->validCreatePayload($clientId), [
            'invoice_id' => (string) $sourceId,
        ]));

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(1, $json['success'] ?? null, 'Body: ' . $response->body());
        self::assertNotSame($sourceId, $json['invoice_id']);
    }

    #[Test]
    public function it_fails_to_copy_an_invoice_without_client_id(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $sourceId = $this->seedInvoice($clientId);
        $payload  = $this->validCreatePayload($clientId);
        unset($payload['client_id']);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/copy_invoice', array_merge($payload, ['invoice_id' => (string) $sourceId]));

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        $this->assertDatabaseCount('ip_invoices', 1);
    }

    #[Test]
    public function it_creates_a_credit_invoice(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $sourceId = $this->seedInvoice($clientId, ['invoice_number' => 'CREDIT-SRC-001']);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/create_credit', array_merge($this->validCreatePayload($clientId), [
            'invoice_id' => (string) $sourceId,
        ]));

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(1, $json['success'] ?? null, 'Body: ' . $response->body());
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $json['invoice_id'], 'creditinvoice_parent_id' => $sourceId]);
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $sourceId, 'is_read_only' => 1]);
    }

    // -------------------------------------------------------------------------
    // read-only helpers
    // -------------------------------------------------------------------------

    #[Test]
    public function it_gets_an_item(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);
        $itemId    = $this->databaseInsert('ip_invoice_items', [
            'invoice_id' => $invoiceId, 'item_tax_rate_id' => 0, 'item_date_added' => date('Y-m-d'),
            'item_name'  => 'Get Me', 'item_description' => '', 'item_quantity' => '1.00', 'item_price' => '10.00', 'item_order' => 0,
        ]);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/get_item', ['item_id' => (string) $itemId]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'Get Me');
    }

    #[Test]
    public function it_gets_a_recur_start_date(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/get_recur_start_date', [
            'invoice_date'    => date('Y-m-d'),
            'recur_frequency' => '1D',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_requires_an_ajax_request(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/invoices/ajax/get_recur_start_date', []);

        /* Assert */
        self::assertSame('', $response->body());
    }

    // -------------------------------------------------------------------------
    // create() — required: client_id, invoice_date_created, invoice_time_created, invoice_group_id
    // -------------------------------------------------------------------------

    private function validCreatePayload(int $clientId): array
    {
        return [
            'client_id'            => (string) $clientId,
            'invoice_date_created' => date('Y-m-d'),
            'invoice_time_created' => date('H:i:s'),
            'invoice_group_id'     => '1',
            'user_id'              => '1',
        ];
    }

    // -------------------------------------------------------------------------
    // save() — required: invoice_date_created, invoice_date_due, invoice_time_created
    // -------------------------------------------------------------------------

    private function validSavePayload(int $invoiceId): array
    {
        return [
            'invoice_id'               => (string) $invoiceId,
            'invoice_date_created'     => date('Y-m-d'),
            'invoice_date_due'         => date('Y-m-d', strtotime('+30 days')),
            'invoice_time_created'     => date('H:i:s'),
            'invoice_status_id'        => '1',
            'invoice_discount_percent' => '0',
            'invoice_discount_amount'  => '0',
            'items'                    => '[]',
        ];
    }
}
