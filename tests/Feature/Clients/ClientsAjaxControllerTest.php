<?php

namespace Tests\Feature\Clients;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Clients AJAX controller — application/modules/clients/controllers/Ajax.php.
 *
 * $ajax_controller = true: every action requires the X-Requested-With XHR
 * header. Covers name_query (client picker search), get_latest, the
 * permissive-search preference toggle, and client-note CRUD. Restores
 * coverage dropped when the old ClientsTest.php was absorbed into
 * ClientsControllerTest without ever creating this file.
 */
#[Group('clients')]
#[CoversClass(\Ajax::class)]
class ClientsAjaxControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // name_query — client picker search
    // -------------------------------------------------------------------------

    #[Test]
    public function it_finds_active_clients_matching_the_query(): void
    {
        /* Arrange */
        $this->seedClient(['client_name' => 'Needle Enterprises', 'client_active' => 1]);
        $this->seedClient(['client_name' => 'Haystack Inc', 'client_active' => 1]);

        /* Act */
        $response = $this->request('GET', '/clients/ajax/name_query', ['query' => 'Needle'], [], true);

        /* Assert */
        $this->assertResponseBodyContains($response, 'Needle Enterprises');
        $this->assertResponseBodyNotContains($response, 'Haystack Inc');
    }

    #[Test]
    public function it_excludes_inactive_clients_from_name_query(): void
    {
        /* Arrange */
        $this->seedClient(['client_name' => 'Inactive Needle Co', 'client_active' => 0]);

        /* Act */
        $response = $this->request('GET', '/clients/ajax/name_query', ['query' => 'Needle'], [], true);

        /* Assert */
        $this->assertResponseBodyNotContains($response, 'Inactive Needle Co');
        self::assertSame([], json_decode($response->body(), true));
    }

    #[Test]
    public function it_returns_an_empty_result_for_name_query_with_no_query(): void
    {
        /* Arrange */
        $this->seedClient(['client_name' => 'Any Client']);

        /* Act */
        $response = $this->request('GET', '/clients/ajax/name_query', [], [], true);

        /* Assert */
        self::assertSame([], json_decode($response->body(), true));
        self::assertSame(200, $response->statusCode());
    }

    #[Test]
    public function it_treats_name_query_input_as_a_literal_search_term(): void
    {
        /*
         * GET /clients/ajax/name_query?query=x' OR '1'='1
         */
        /* Arrange */
        $this->seedClient(['client_name' => 'Real Client']);

        /* Act */
        $response = $this->request('GET', '/clients/ajax/name_query', ['query' => "x' OR '1'='1"], [], true);

        /* Assert */
        self::assertSame([], json_decode($response->body(), true), 'A SQLi-shaped query must be treated as a literal string, matching nothing.');
        $this->assertResponseBodyNotContains($response, 'Real Client');
    }

    // -------------------------------------------------------------------------
    // get_latest
    // -------------------------------------------------------------------------

    #[Test]
    public function it_returns_up_to_five_latest_active_clients(): void
    {
        /* Arrange */
        for ($i = 0; $i < 7; $i++) {
            $this->seedClient(['client_name' => 'Latest Client ' . $i, 'client_active' => 1]);
        }

        /* Act */
        $response = $this->request('GET', '/clients/ajax/get_latest', [], [], true);
        $json     = json_decode($response->body(), true);

        /* Assert */
        self::assertCount(5, $json, 'get_latest must cap the result at 5 rows.');
        self::assertSame(200, $response->statusCode());
    }

    #[Test]
    public function it_escapes_client_names_returned_by_get_latest(): void
    {
        /* Arrange */
        $this->seedClient(['client_name' => '<script>alert(1)</script>', 'client_active' => 1]);

        /* Act */
        $response = $this->request('GET', '/clients/ajax/get_latest', [], [], true);
        $json     = json_decode($response->body(), true);

        /* Assert */
        $this->assertResponseBodyNotContains($response, '<script>alert(1)</script>');
        self::assertContains('&lt;script&gt;alert(1)&lt;/script&gt;', array_column($json, 'text'));
    }

    // -------------------------------------------------------------------------
    // save_preference_permissive_search_clients
    // -------------------------------------------------------------------------

    #[Test]
    public function it_saves_a_valid_permissive_search_preference(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->request('GET', '/clients/ajax/save_preference_permissive_search_clients', ['permissive_search_clients' => '1'], [], true);

        /* Assert */
        self::assertSame(200, $response->statusCode());
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'enable_permissive_search_clients', 'setting_value' => '1']);
    }

    #[Test]
    public function it_rejects_an_invalid_permissive_search_preference_value(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->request('GET', '/clients/ajax/save_preference_permissive_search_clients', ['permissive_search_clients' => '2'], [], true);

        /* Assert */
        self::assertSame(200, $response->statusCode());
        $this->assertDatabaseMissing('ip_settings', ['setting_key' => 'enable_permissive_search_clients']);
    }

    // -------------------------------------------------------------------------
    // save_client_note — happy path + required-field validation
    // -------------------------------------------------------------------------

    #[Test]
    public function it_saves_a_client_note_with_all_required_fields(): void
    {
        /**
         * POST /clients/ajax/save_client_note
         * { "client_id": "<seeded client id>", "client_note": "A note about this client" }.
         */
        /* Arrange */
        $clientId = $this->seedClient();

        /* Act */
        $response = $this->ajax('POST', '/clients/ajax/save_client_note', [
            'client_id'   => (string) $clientId,
            'client_note' => 'A note about this client',
        ]);
        $json = json_decode($response->body(), true);

        /* Assert */
        self::assertSame(1, $json['success'] ?? null, 'Save failed: ' . $response->body());
        $this->assertDatabaseHas('ip_client_notes', ['client_id' => $clientId, 'client_note' => 'A note about this client']);
    }

    #[Test]
    public function it_fails_to_save_a_client_note_without_client_id(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->ajax('POST', '/clients/ajax/save_client_note', [
            'client_id'   => '',
            'client_note' => 'A note about this client',
        ]);
        $json = json_decode($response->body(), true);

        /* Assert */
        self::assertSame(0, $json['success'] ?? null);
        $this->assertDatabaseCount('ip_client_notes', 0);
    }

    #[Test]
    public function it_fails_to_save_a_client_note_without_client_note_text(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();

        /* Act */
        $response = $this->ajax('POST', '/clients/ajax/save_client_note', [
            'client_id'   => (string) $clientId,
            'client_note' => '',
        ]);
        $json = json_decode($response->body(), true);

        /* Assert */
        self::assertSame(0, $json['success'] ?? null);
        $this->assertDatabaseCount('ip_client_notes', 0);
    }

    // -------------------------------------------------------------------------
    // delete_client_note
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_an_existing_client_note(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $noteId   = $this->databaseInsert('ip_client_notes', [
            'client_id'        => $clientId,
            'client_note'      => 'Note to delete',
            'client_note_date' => date('Y-m-d'),
        ]);

        /* Act */
        $response = $this->ajax('POST', '/clients/ajax/delete_client_note', ['client_note_id' => (string) $noteId]);
        $json     = json_decode($response->body(), true);

        /* Assert */
        self::assertSame(1, $json['success'] ?? null);
        $this->assertDatabaseMissing('ip_client_notes', ['client_note_id' => $noteId]);
    }

    #[Test]
    public function it_does_not_delete_anything_for_a_nonexistent_note_id(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $noteId   = $this->databaseInsert('ip_client_notes', [
            'client_id'        => $clientId,
            'client_note'      => 'Untouched note',
            'client_note_date' => date('Y-m-d'),
        ]);

        /* Act */
        $response = $this->ajax('POST', '/clients/ajax/delete_client_note', ['client_note_id' => '999999']);
        $json     = json_decode($response->body(), true);

        /* Assert */
        self::assertSame(0, $json['success'] ?? null);
        $this->assertDatabaseHas('ip_client_notes', ['client_note_id' => $noteId]);
    }

    // -------------------------------------------------------------------------
    // load_client_notes
    // -------------------------------------------------------------------------

    #[Test]
    public function it_loads_notes_for_a_client(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $this->databaseInsert('ip_client_notes', [
            'client_id'        => $clientId,
            'client_note'      => 'Visible note marker',
            'client_note_date' => date('Y-m-d'),
        ]);

        /* Act */
        $response = $this->ajax('POST', '/clients/ajax/load_client_notes', ['client_id' => (string) $clientId]);

        /* Assert */
        $this->assertResponseBodyContains($response, 'Visible note marker');
        self::assertSame(200, $response->statusCode());
    }

    // -------------------------------------------------------------------------
    // Edge cases
    // -------------------------------------------------------------------------

    #[Test]
    public function it_requires_an_ajax_request(): void
    {
        /* Arrange */
        $this->seedClient(['client_name' => 'Should Not Appear', 'client_active' => 1]);

        /* Act */
        // Base_Controller's ajax_controller guard is a bare `exit;`, not a
        // 404/403 — the response is 200 with an empty body.
        $response = $this->get('/clients/ajax/get_latest');

        /* Assert */
        self::assertSame('', $response->body());
        $this->assertResponseBodyNotContains($response, 'Should Not Appear');
    }
}
