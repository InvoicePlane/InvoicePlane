<?php

declare(strict_types=1);

namespace Tests\Feature\Clients;

use Clients;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class ClientsTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function it_deletes_a_client_and_cascades_its_orphaned_invoice_and_notes(): void
    {
        $this->setUpClientDeletionValidation();

        /* Arrange */

        $clientId = $this->seedClient(['client_name' => 'Cascade Delete Client']);

        $invoiceId = $this->seedInvoice($clientId);

        $noteId = $this->databaseInsert('ip_client_notes', [
            'client_id' => $clientId,

            'client_note_date' => date('Y-m-d'),

            'client_note' => 'A note that should be cleaned up',
        ]);

        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId]);

        $this->assertDatabaseHas('ip_client_notes', ['client_note_id' => $noteId]);

        /* Act */

        $response = $this->post('/clients/delete/' . $clientId, []);

        /* Assert */

        self::assertTrue($response->isRedirect(), 'Delete must redirect.');

        $this->assertDatabaseMissing('ip_clients', ['client_id' => $clientId]);

        $this->assertDatabaseMissing('ip_invoices', ['invoice_id' => $invoiceId]);

        $this->assertDatabaseMissing('ip_client_notes', ['client_note_id' => $noteId]);
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        $this->setUpClientDeletionValidation();

        /* Arrange */

        $this->actingAsGuest();

        /* Act */

        $response = $this->get('/clients/status/active');

        /* Assert */

        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/clients] must redirect. Got [%d].', $response->statusCode())
        );
    }

    // -------------------------------------------------------------------------

    // name_query

    // -------------------------------------------------------------------------
    #[Test]
    public function it_finds_active_clients_matching_the_query(): void
    {
        $this->setUpClientsAjaxController();

        /* Arrange */

        $this->seedClient(['client_name' => 'Needle Enterprises', 'client_active' => 1]);

        $this->seedClient(['client_name' => 'Haystack Inc', 'client_active' => 1]);

        /* Act */

        $response = $this->request('GET', '/clients/ajax/name_query', ['query' => 'Needle'], [], true);

        /* Assert */

        $this->assertResponseStatusCode($response, 200);

        $this->assertResponseBodyContains($response, 'Needle Enterprises');

        $this->assertResponseBodyNotContains($response, 'Haystack Inc');
    }

    #[Test]
    public function it_excludes_inactive_clients_from_name_query(): void
    {
        $this->setUpClientsAjaxController();

        /* Arrange */

        $this->seedClient(['client_name' => 'Inactive Needle Co', 'client_active' => 0]);

        /* Act */

        $response = $this->request('GET', '/clients/ajax/name_query', ['query' => 'Needle'], [], true);

        /* Assert */

        $this->assertResponseBodyNotContains($response, 'Inactive Needle Co');
    }

    #[Test]
    public function it_returns_an_empty_result_for_name_query_with_no_query(): void
    {
        $this->setUpClientsAjaxController();

        /* Arrange */

        $this->seedClient(['client_name' => 'Any Client']);

        /* Act */

        $response = $this->request('GET', '/clients/ajax/name_query', [], [], true);

        /* Assert */

        $this->assertResponseStatusCode($response, 200);

        self::assertSame([], json_decode($response->body(), true));
    }

    #[Test]
    public function it_treats_name_query_input_as_a_literal_search_term(): void
    {
        $this->setUpClientsAjaxController();

        /* Arrange */

        $this->seedClient(['client_name' => 'Real Client']);

        /* Act */

        $response = $this->request('GET', '/clients/ajax/name_query', ['query' => "x' OR '1'='1"], [], true);

        /* Assert */

        $this->assertResponseStatusCode($response, 200);

        $this->assertResponseHasNoPhpErrors($response);

        $this->assertResponseBodyNotContains($response, 'Real Client');
    }

    // -------------------------------------------------------------------------

    // get_latest

    // -------------------------------------------------------------------------
    #[Test]
    public function it_returns_up_to_five_latest_active_clients(): void
    {
        $this->setUpClientsAjaxController();

        /* Arrange */

        for ($i = 0; $i < 7; $i++) {
            $this->seedClient(['client_name' => 'Latest Client ' . $i, 'client_active' => 1]);
        }

        /* Act */

        $response = $this->request('GET', '/clients/ajax/get_latest', [], [], true);

        /* Assert */

        $this->assertResponseStatusCode($response, 200);

        $json = json_decode($response->body(), true);

        self::assertCount(5, $json);
    }

    #[Test]
    public function it_escapes_client_names_returned_by_get_latest(): void
    {
        $this->setUpClientsAjaxController();

        /* Arrange */

        $this->seedClient(['client_name' => '<script>alert(1)</script>', 'client_active' => 1]);

        /* Act */

        $response = $this->request('GET', '/clients/ajax/get_latest', [], [], true);

        /* Assert */

        $this->assertResponseBodyNotContains($response, '<script>alert(1)</script>');

        $json = json_decode($response->body(), true);

        self::assertContains('&lt;script&gt;alert(1)&lt;/script&gt;', array_column($json, 'text'));
    }

    // -------------------------------------------------------------------------

    // save_preference_permissive_search_clients

    // -------------------------------------------------------------------------
    #[Test]
    public function it_saves_a_valid_permissive_search_preference(): void
    {
        $this->setUpClientsAjaxController();

        /* Arrange */

        /* Act */

        $this->request('GET', '/clients/ajax/save_preference_permissive_search_clients', ['permissive_search_clients' => '1'], [], true);

        /* Assert */

        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'enable_permissive_search_clients', 'setting_value' => '1']);
    }

    #[Test]
    public function it_rejects_an_invalid_permissive_search_preference_value(): void
    {
        $this->setUpClientsAjaxController();

        /* Arrange */

        /* Act */

        $this->request('GET', '/clients/ajax/save_preference_permissive_search_clients', ['permissive_search_clients' => '2'], [], true);

        /* Assert */

        $this->assertDatabaseMissing('ip_settings', ['setting_key' => 'enable_permissive_search_clients']);
    }

    // -------------------------------------------------------------------------

    // save_client_note (required-field validation)

    // -------------------------------------------------------------------------
    #[Test]
    public function it_saves_a_client_note_with_all_required_fields(): void
    {
        $this->setUpClientsAjaxController();

        /* Arrange */

        $clientId = $this->seedClient();

        /* Act */

        $response = $this->ajax('POST', '/clients/ajax/save_client_note', [
            'client_id' => (string) $clientId,

            'client_note' => 'A note about this client',
        ]);

        /* Assert */

        $json = json_decode($response->body(), true);

        self::assertSame(1, $json['success'] ?? null);

        $this->assertDatabaseHas('ip_client_notes', ['client_id' => $clientId, 'client_note' => 'A note about this client']);
    }

    #[Test]
    public function it_fails_to_save_a_client_note_without_client_id(): void
    {
        $this->setUpClientsAjaxController();

        /* Arrange */

        /* Act */

        $response = $this->ajax('POST', '/clients/ajax/save_client_note', [
            'client_id' => '',

            'client_note' => 'A note about this client',
        ]);

        /* Assert */

        $json = json_decode($response->body(), true);

        self::assertSame(0, $json['success'] ?? null);

        $this->assertDatabaseCount('ip_client_notes', 0);
    }

    #[Test]
    public function it_fails_to_save_a_client_note_without_client_note_text(): void
    {
        $this->setUpClientsAjaxController();

        /* Arrange */

        $clientId = $this->seedClient();

        /* Act */

        $response = $this->ajax('POST', '/clients/ajax/save_client_note', [
            'client_id' => (string) $clientId,

            'client_note' => '',
        ]);

        /* Assert */

        $json = json_decode($response->body(), true);

        self::assertSame(0, $json['success'] ?? null);

        $this->assertDatabaseCount('ip_client_notes', 0);
    }

    // -------------------------------------------------------------------------

    // delete_client_note

    // -------------------------------------------------------------------------
    #[Test]
    public function it_deletes_an_existing_client_note(): void
    {
        $this->setUpClientsAjaxController();

        /* Arrange */

        $clientId = $this->seedClient();

        $noteId = $this->databaseInsert('ip_client_notes', [
            'client_id' => $clientId,

            'client_note' => 'Note to delete',

            'client_note_date' => date('Y-m-d'),
        ]);

        /* Act */

        $response = $this->ajax('POST', '/clients/ajax/delete_client_note', ['client_note_id' => (string) $noteId]);

        /* Assert */

        $json = json_decode($response->body(), true);

        self::assertSame(1, $json['success'] ?? null);

        $this->assertDatabaseMissing('ip_client_notes', ['client_note_id' => $noteId]);
    }

    #[Test]
    public function it_does_not_delete_anything_for_a_nonexistent_note_id(): void
    {
        $this->setUpClientsAjaxController();

        /* Arrange */

        $clientId = $this->seedClient();

        $noteId = $this->databaseInsert('ip_client_notes', [
            'client_id' => $clientId,

            'client_note' => 'Untouched note',

            'client_note_date' => date('Y-m-d'),
        ]);

        /* Act */

        $response = $this->ajax('POST', '/clients/ajax/delete_client_note', ['client_note_id' => '999999']);

        /* Assert */

        $json = json_decode($response->body(), true);

        self::assertSame(0, $json['success'] ?? null);

        $this->assertDatabaseHas('ip_client_notes', ['client_note_id' => $noteId]);
    }

    // -------------------------------------------------------------------------

    // load_client_notes

    // -------------------------------------------------------------------------
    #[Test]
    public function it_loads_notes_for_a_client(): void
    {
        $this->setUpClientsAjaxController();

        /* Arrange */

        $clientId = $this->seedClient();

        $this->databaseInsert('ip_client_notes', [
            'client_id' => $clientId,

            'client_note' => 'Visible note marker',

            'client_note_date' => date('Y-m-d'),
        ]);

        /* Act */

        $response = $this->ajax('POST', '/clients/ajax/load_client_notes', ['client_id' => (string) $clientId]);

        /* Assert */

        $this->assertResponseStatusCode($response, 200);

        $this->assertResponseBodyContains($response, 'Visible note marker');
    }

    #[Test]
    public function it_requires_an_ajax_request(): void
    {
        $this->setUpClientsAjaxController();

        /* Arrange */

        $this->seedClient(['client_name' => 'Should Not Appear', 'client_active' => 1]);

        /* Act: Base_Controller's ajax_controller guard is a bare `exit;`, not a

         * 404/403 — the response is 200 with an empty body. */

        $response = $this->get('/clients/ajax/get_latest');

        /* Assert */

        self::assertSame('', $response->body());
    }

    // -------------------------------------------------------------------------

    // List

    // -------------------------------------------------------------------------
    #[Test]
    public function it_lists_active_clients(): void
    {
        $this->setUpClientsFeature();

        /* Arrange */

        $this->seedClient(['client_name' => 'Listed Corp', 'client_active' => 1]);

        /* Act */

        $response = $this->get('/clients/status/active');

        /* Assert */

        $this->assertResponseStatusCode($response, 200);

        $this->assertResponseBodyContains($response, 'Listed Corp');
    }

    #[Test]
    public function it_lists_inactive_clients(): void
    {
        $this->setUpClientsFeature();

        /* Arrange */

        $this->seedClient(['client_name' => 'Dormant Inc', 'client_active' => 0]);

        /* Act */

        $response = $this->get('/clients/status/inactive');

        /* Assert */

        $this->assertResponseStatusCode($response, 200);

        $this->assertResponseBodyContains($response, 'Dormant Inc');
    }

    // -------------------------------------------------------------------------

    // Create

    // -------------------------------------------------------------------------
    #[Test]
    public function it_renders_the_create_client_form(): void
    {
        $this->setUpClientsFeature();

        /* Arrange */

        /* (admin session set in setUpClientsFeature) */

        /* Act */

        $response = $this->get('/clients/form');

        /* Assert */

        $this->assertResponseStatusCode($response, 200);

        $this->assertResponseBodyContains($response, '<form');
    }

    #[Test]
    public function it_creates_a_client(): void
    {
        $this->setUpClientsFeature();

        /**
         * POST /clients/form.
         *
         * {
         *
         *     "client_name": "Acme Corp",
         *
         *     "btn_submit": "1",
         *
         *     "is_update": "0"
         *
         * }.
         */

        /* Arrange */

        /* (admin session set in setUpClientsFeature) */

        /* Act */

        $response = $this->post('/clients/form', [
            'client_name' => 'Acme Corp',

            'btn_submit' => '1',

            'is_update' => '0',
        ]);

        /* Assert */

        $client = $this->databaseFetchOne('ip_clients', ['client_name' => 'Acme Corp']);

        self::assertNotNull($client);

        $this->assertResponseRedirectsToRoute($response, 'clients/view/' . $client['client_id']);
    }

    #[Test]
    public function it_creates_a_client_with_full_details(): void
    {
        $this->setUpClientsFeature();

        /**
         * POST /clients/form.
         *
         * {
         *
         *     "client_name": "Full Details Ltd",
         *
         *     "client_surname": "Smith",
         *
         *     "client_email": "full@example.com",
         *
         *     "client_phone": "+31612345678",
         *
         *     "client_city": "Amsterdam",
         *
         *     "client_country": "NL",
         *
         *     "btn_submit": "1",
         *
         *     "is_update": "0"
         *
         * }.
         */

        /* Arrange */

        /* (admin session set in setUpClientsFeature) */

        /* Act */

        $response = $this->post('/clients/form', [
            'client_name' => 'Full Details Ltd',

            'client_surname' => 'Smith',

            'client_email' => 'full@example.com',

            'client_phone' => '+31612345678',

            'client_city' => 'Amsterdam',

            'client_country' => 'NL',

            'btn_submit' => '1',

            'is_update' => '0',
        ]);

        /* Assert */

        $client = $this->databaseFetchOne('ip_clients', ['client_name' => 'Full Details Ltd']);

        self::assertNotNull($client);

        $this->assertResponseRedirectsToRoute($response, 'clients/view/' . $client['client_id']);

        $this->assertDatabaseHas('ip_clients', [
            'client_name' => 'Full Details Ltd',

            'client_email' => 'full@example.com',

            'client_city' => 'Amsterdam',
        ]);
    }

    // -------------------------------------------------------------------------

    // Update

    // -------------------------------------------------------------------------
    #[Test]
    public function it_renders_the_edit_form_showing_existing_client_name(): void
    {
        $this->setUpClientsFeature();

        /* Arrange */

        $clientId = $this->seedClient(['client_name' => 'Editable Corp']);

        /* Act */

        $response = $this->get('/clients/form/' . $clientId);

        /* Assert */

        $this->assertResponseStatusCode($response, 200);

        $this->assertResponseBodyContains($response, '<form');

        $this->assertResponseBodyContains($response, 'Editable Corp');
    }

    #[Test]
    public function it_updates_a_client(): void
    {
        $this->setUpClientsFeature();

        /**
         * POST /clients/form/{id}.
         *
         * {
         *
         *     "client_name": "Renamed Corp",
         *
         *     "btn_submit": "1",
         *
         *     "is_update": "1"
         *
         * }.
         */

        /* Arrange */

        $clientId = $this->seedClient(['client_name' => 'Original Name']);

        /* Act */

        $response = $this->post('/clients/form/' . $clientId, [
            'client_name' => 'Renamed Corp',

            'btn_submit' => '1',

            'is_update' => '1',
        ]);

        /* Assert */

        $this->assertResponseRedirectsToRoute($response, 'clients/view/' . $clientId);

        $this->assertDatabaseHas('ip_clients', ['client_id' => $clientId, 'client_name' => 'Renamed Corp']);

        $this->assertDatabaseMissing('ip_clients', ['client_id' => $clientId, 'client_name' => 'Original Name']);
    }

    // -------------------------------------------------------------------------

    // Delete

    // -------------------------------------------------------------------------
    #[Test]
    public function it_deletes_a_client(): void
    {
        $this->setUpClientsFeature();

        /* Arrange */

        $clientId = $this->seedClient(['client_name' => 'Deletable Corp']);

        $this->assertDatabaseHas('ip_clients', ['client_id' => $clientId]);

        /* Act */

        $response = $this->post('/clients/delete/' . $clientId, []);

        /* Assert */

        $this->assertResponseRedirectsToRoute($response, 'clients');

        $this->assertDatabaseMissing('ip_clients', ['client_id' => $clientId]);
    }

    // -------------------------------------------------------------------------

    // Validation failures — missing required fields

    // -------------------------------------------------------------------------
    #[Test]
    public function it_fails_to_create_without_client_name(): void
    {
        $this->setUpClientsFeature();

        /**
         * POST /clients/form.
         *
         * {
         *
         *     "client_name": "",
         *
         *     "btn_submit": "1",
         *
         *     "is_update": "0"
         *
         * }.
         */

        /* Arrange */

        /* (admin session set in setUpClientsFeature) */

        /* Act */

        $response = $this->post('/clients/form', [
            'client_name' => '',

            'btn_submit' => '1',

            'is_update' => '0',
        ]);

        /* Assert */

        // Validation failure in CI3 re-renders the form at 200

        $this->assertResponseStatusCode($response, 200);

        $this->assertResponseBodyContains($response, '<form');

        $this->assertDatabaseCount('ip_clients', 0);
    }

    #[Test]
    public function it_fails_to_update_without_client_name(): void
    {
        $this->setUpClientsFeature();

        /**
         * POST /clients/form/{id}.
         *
         * {
         *
         *     "client_name": "",
         *
         *     "btn_submit": "1",
         *
         *     "is_update": "1"
         *
         * }.
         */

        /* Arrange */

        $clientId = $this->seedClient(['client_name' => 'Will Not Change']);

        /* Act */

        $response = $this->post('/clients/form/' . $clientId, [
            'client_name' => '',

            'btn_submit' => '1',

            'is_update' => '1',
        ]);

        /* Assert */

        // Validation failure in CI3 re-renders the form at 200

        $this->assertResponseStatusCode($response, 200);

        $this->assertResponseBodyContains($response, '<form');

        $this->assertDatabaseHas('ip_clients', ['client_id' => $clientId, 'client_name' => 'Will Not Change']);
    }

    // -------------------------------------------------------------------------

    // View / edge cases

    // -------------------------------------------------------------------------
    #[Test]
    public function it_views_a_single_client_and_shows_the_client_name(): void
    {
        $this->setUpClientsFeature();

        /* Arrange */

        $clientId = $this->seedClient(['client_name' => 'View Me Corp']);

        /* Act */

        $response = $this->get('/clients/view/' . $clientId);

        /* Assert */

        $this->assertResponseStatusCode($response, 200);

        $this->assertResponseBodyContains($response, 'View Me Corp');
    }

    #[Test]
    public function it_returns_404_when_viewing_a_nonexistent_client(): void
    {
        $this->setUpClientsFeature();

        /* Arrange */

        /* (admin session set in setUpClientsFeature) */

        /* Act */

        $response = $this->get('/clients/view/999999999');

        /* Assert */

        $this->assertResponseBodyContains($response, 'Not Found');
    }

    #[Test]
    public function it_redirects_when_creating_a_duplicate_client(): void
    {
        $this->setUpClientsFeature();

        /*

         * POST /clients/form (duplicate)

         * {

         *     "client_name": "Duplicate Corp",

         *     "btn_submit": "1",

         *     "is_update": "0"

         * }

         */

        /* Arrange */

        $this->seedClient(['client_name' => 'Duplicate Corp']);

        /* Act */

        $response = $this->post('/clients/form', [
            'client_name' => 'Duplicate Corp',

            'btn_submit' => '1',

            'is_update' => '0',
        ]);

        /* Assert */

        // CI3 controller redirects back with a flash error when the client already exists

        self::assertTrue($response->isRedirect(), 'Creating a duplicate client must redirect.');

        $this->assertDatabaseCount('ip_clients', 1, ['client_name' => 'Duplicate Corp']);
    }

    #[Test]
    public function it_redirects_index_to_active_client_list(): void
    {
        $this->setUpClientsFeature();

        /* Arrange */

        /* (admin session set in setUpClientsFeature) */

        /* Act */

        $response = $this->get('/clients');

        /* Assert */

        self::assertTrue($response->isRedirect(), 'GET /clients must redirect to status/active.');
    }

    // -------------------------------------------------------------------------

    // Guest redirect — always last

    // -------------------------------------------------------------------------
    #[Test]
    public function it_redirects_a_guest_to_login_from_clientsfeature(): void
    {
        $this->setUpClientsFeature();

        /* Arrange */

        $this->actingAsGuest();

        /* Act */

        $response = $this->get('/clients/status/active');

        /* Assert */

        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
    }

    protected function setUpClientDeletionValidation(): void
    {
        $this->actingAsAdmin();
    }

    protected function setUpClientsAjaxController(): void
    {
        $this->actingAsAdmin();
    }

    protected function setUpClientsFeature(): void
    {
        $this->actingAsAdmin();
    }
}
