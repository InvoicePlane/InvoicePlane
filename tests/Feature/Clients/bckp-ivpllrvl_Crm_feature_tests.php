<?php

namespace Modules\Crm\tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

use function Modules\Clients\Tests\Feature\route;

use Modules\Crm\app\Http\Controllers\AjaxController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(AjaxController::class)]
class AjaxControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_clients_matching_name_query()
    {
        // Arrange: create clients
        $client = \Modules\Clients\Models\tmpClient::factory()->create(['name' => 'Test Client']);

        /**
         * Payload:
         * {
         *   "query": "Test"
         * }
         */
        // Act: query for clients by name
        $response = $this->json('POST', route('clients.ajax.nameQuery'), [
            'query' => 'Test',
        ]);

        // Assert: client is returned
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Test Client']);
    }

    #[Test]
    public function it_gets_latest_clients()
    {
        // Arrange: create clients
        $client = \Modules\Clients\Models\tmpClient::factory()->create();

        // Act: get latest clients
        $response = $this->get(route('clients.ajax.getLatest'));

        // Assert: clients are returned
        $response->assertStatus(200);
    }

    #[Test]
    public function it_saves_permissive_search_preference()
    {
        /**
         * Payload:
         * {
         *   "permissive_search_clients": "1"
         * }
         */
        // Act: save preference
        $response = $this->json('POST', route('clients.ajax.savePreference'), [
            'permissive_search_clients' => '1',
        ]);

        // Assert: preference is saved
        $response->assertStatus(200);
    }

    #[Test]
    public function it_deletes_client_note()
    {
        // Arrange: create client and note
        $client = \Modules\Clients\Models\tmpClient::factory()->create();
        $note   = \Modules\Crm\app\Models\ClientNote::factory()->create(['client_id' => $client->id]);

        // Act: delete note
        $response = $this->json('POST', route('clients.ajax.deleteNote', ['note_id' => $note->id]));

        // Assert: note is deleted
        $response->assertStatus(200);
        $this->assertDatabaseMissing('ip_client_notes', ['id' => $note->id]);
    }

    #[Test]
    public function it_saves_client_note()
    {
        // Arrange: create client
        $client = \Modules\Clients\Models\tmpClient::factory()->create();

        /**
         * Payload:
         * {
         *   "client_id": 1,
         *   "note": "This is a test note"
         * }
         */
        // Act: save note
        $response = $this->json('POST', route('clients.ajax.saveNote'), [
            'client_id' => $client->id,
            'note'      => 'This is a test note',
        ]);

        // Assert: note is saved
        $response->assertStatus(200);
        $this->assertDatabaseHas('ip_client_notes', [
            'client_id' => $client->id,
            'note'      => 'This is a test note',
        ]);
    }

    #[Test]
    public function it_loads_client_notes()
    {
        // Arrange: create client and notes
        $client = \Modules\Clients\Models\tmpClient::factory()->create();
        $note   = \Modules\Crm\app\Models\ClientNote::factory()->create(['client_id' => $client->id]);

        // Act: load notes
        $response = $this->get(route('clients.ajax.loadNotes', ['client_id' => $client->id]));

        // Assert: notes are returned
        $response->assertStatus(200);
        $response->assertSee($note->note);
    }
}

#[CoversClass(AjaxController::class)]
class ClientsAjaxControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_type' => 1]);
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_returns_empty_json_when_query_is_empty()
    {
        $response = $this->get(route('clients.ajax.nameQuery'));

        $response->assertSuccessful();
        $response->assertJson([]);
    }

    /** @test */
    public function it_searches_clients_by_name_with_trailing_wildcard()
    {
        Client::factory()->create(['client_name' => 'John', 'client_active' => 1]);
        Client::factory()->create(['client_name' => 'Jane', 'client_active' => 1]);
        Client::factory()->create(['client_name' => 'Bob', 'client_active' => 1]);

        $response = $this->get(route('clients.ajax.nameQuery', ['query' => 'J']));

        $data = $response->json();
        $this->assertCount(2, $data);
        $this->assertTrue(str_contains($data[0]['text'], 'J'));
    }

    /** @test */
    public function it_searches_clients_with_leading_wildcard_when_permissive_search_enabled()
    {
        Client::factory()->create(['client_name' => 'John Doe', 'client_active' => 1]);
        Client::factory()->create(['client_surname' => 'Johnson', 'client_active' => 1]);

        $response = $this->get(route('clients.ajax.nameQuery', [
            'query'                     => 'ohn',
            'permissive_search_clients' => 1,
        ]));

        $data = $response->json();
        $this->assertGreaterThanOrEqual(2, count($data));
    }

    /** @test */
    public function it_only_returns_active_clients_in_name_query()
    {
        Client::factory()->create(['client_name' => 'Active Client', 'client_active' => 1]);
        Client::factory()->create(['client_name' => 'Inactive Client', 'client_active' => 0]);

        $response = $this->get(route('clients.ajax.nameQuery', ['query' => 'Client']));

        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertStringContainsString('Active', $data[0]['text']);
    }

    /** @test */
    public function it_escapes_percent_signs_in_search_query()
    {
        Client::factory()->create(['client_name' => '100% Good', 'client_active' => 1]);

        $response = $this->get(route('clients.ajax.nameQuery', ['query' => '100%']));

        $response->assertSuccessful();
        // Should not cause SQL error
    }

    /** @test */
    public function it_returns_five_most_recent_active_clients()
    {
        Client::factory()->count(10)->create(['client_active' => 1]);

        $response = $this->get(route('clients.ajax.getLatest'));

        $data = $response->json();
        $this->assertCount(5, $data);
    }

    /** @test */
    public function it_saves_permissive_search_preference_with_valid_value()
    {
        $response = $this->get(route('clients.ajax.savePreference', ['permissive_search_clients' => '1']));

        $response->assertSuccessful();
        $this->assertEquals('1', get_setting('enable_permissive_search_clients'));
    }

    /** @test */
    public function it_rejects_invalid_permissive_search_preference_value()
    {
        $response = $this->get(route('clients.ajax.savePreference', ['permissive_search_clients' => '2']));

        // Should exit without saving
        $this->assertNotEquals('2', get_setting('enable_permissive_search_clients'));
    }

    /** @test */
    public function it_successfully_deletes_client_note()
    {
        $client = Client::factory()->create();
        $note   = ClientNote::factory()->create(['client_id' => $client->client_id]);

        $response = $this->post(route('clients.ajax.deleteNote'), [
            'client_note_id' => $note->client_note_id,
        ]);

        $response->assertJson(['success' => 1]);
        $this->assertDatabaseMissing('ip_client_notes', ['client_note_id' => $note->client_note_id]);
    }

    /** @test */
    public function it_returns_success_false_when_deleting_nonexistent_note()
    {
        $response = $this->post(route('clients.ajax.deleteNote'), [
            'client_note_id' => 99999,
        ]);

        $response->assertJson(['success' => 0]);
    }

    /** @test */
    public function it_saves_client_note_with_valid_data()
    {
        $client = Client::factory()->create();

        $response = $this->post(route('clients.ajax.saveNote'), [
            'client_id'           => $client->client_id,
            'client_note_content' => 'Test note content',
            csrf_token()          => csrf_token(),
        ]);

        $response->assertJson(['success' => 1]);
        $this->assertDatabaseHas('ip_client_notes', [
            'client_id'           => $client->client_id,
            'client_note_content' => 'Test note content',
        ]);
    }

    /** @test */
    public function it_returns_validation_errors_when_saving_invalid_note()
    {
        $response = $this->post(route('clients.ajax.saveNote'), [
            'client_id'           => null,
            'client_note_content' => '',
            csrf_token()          => csrf_token(),
        ]);

        $response->assertJson(['success' => 0]);
        $this->assertArrayHasKey('validation_errors', $response->json());
    }

    /** @test */
    public function it_returns_new_csrf_token_after_saving_note()
    {
        $client = Client::factory()->create();

        $response = $this->post(route('clients.ajax.saveNote'), [
            'client_id'           => $client->client_id,
            'client_note_content' => 'Test',
            csrf_token()          => csrf_token(),
        ]);

        $data = $response->json();
        $this->assertArrayHasKey('new_token', $data);
        $this->assertNotEmpty($data['new_token']);
    }

    /** @test */
    public function it_loads_all_notes_for_specific_client()
    {
        $client      = Client::factory()->create();
        $otherClient = Client::factory()->create();

        ClientNote::factory()->count(3)->create(['client_id' => $client->client_id]);
        ClientNote::factory()->count(2)->create(['client_id' => $otherClient->client_id]);

        $response = $this->post(route('clients.ajax.loadNotes'), [
            'client_id' => $client->client_id,
        ]);

        $response->assertSuccessful();
        $response->assertViewHas('client_notes', function ($notes) {
            return count($notes) === 3;
        });
    }
}

#[CoversClass(ClientsController::class)]
class ClientsControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_type' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_redirects_to_active_clients_status()
    {
        // Act: visit clients index
        $response = $this->get(route('clients.index'));

        // Assert: redirects to active status
        $response->assertRedirect(route('clients.status', ['status' => 'active']));
    }

    #[Test]
    public function it_displays_active_clients()
    {
        // Arrange: create active and inactive clients
        $activeClient   = tmpClient::factory()->create(['active' => 1]);
        $inactiveClient = tmpClient::factory()->create(['active' => 0]);

        // Act: visit active clients page
        $response = $this->get(route('clients.status', ['status' => 'active']));

        // Assert: only active client is displayed
        $response->assertStatus(200);
        $response->assertViewIs('clients.index');
        $response->assertViewHas('records', function ($clients) use ($activeClient, $inactiveClient) {
            return $clients->contains($activeClient) && ! $clients->contains($inactiveClient);
        });
        $response->assertSee($activeClient->name);
        $response->assertDontSee($inactiveClient->name);
    }

    #[Test]
    public function it_displays_inactive_clients()
    {
        // Arrange: create active and inactive clients
        $activeClient   = tmpClient::factory()->create(['active' => 1]);
        $inactiveClient = tmpClient::factory()->create(['active' => 0]);

        // Act: visit inactive clients page
        $response = $this->get(route('clients.status', ['status' => 'inactive']));

        // Assert: only inactive client is displayed
        $response->assertStatus(200);
        $response->assertViewIs('clients.index');
        $response->assertViewHas('records', function ($clients) use ($activeClient, $inactiveClient) {
            return ! $clients->contains($activeClient) && $clients->contains($inactiveClient);
        });
        $response->assertSee($inactiveClient->name);
        $response->assertDontSee($activeClient->name);
    }

    #[Test]
    public function it_displays_all_clients()
    {
        // Arrange: create active and inactive clients
        $activeClient   = tmpClient::factory()->create(['active' => 1]);
        $inactiveClient = tmpClient::factory()->create(['active' => 0]);

        // Act: visit all clients page
        $response = $this->get(route('clients.status', ['status' => 'all']));

        // Assert: both clients are displayed
        $response->assertStatus(200);
        $response->assertViewIs('clients.index');
        $response->assertViewHas('records', function ($clients) use ($activeClient, $inactiveClient) {
            return $clients->contains($activeClient) && $clients->contains($inactiveClient);
        });
        $response->assertSee($activeClient->name);
        $response->assertSee($inactiveClient->name);
    }

    #[Test]
    public function it_cancels_client_form_and_redirects_to_index()
    {
        $response = $this->post(route('clients.form'), ['btn_cancel' => true]);

        $response->assertRedirect(route('clients.index'));
    }

    #[Test]
    public function it_displays_client_form_for_new_client()
    {
        // Act: visit new client form
        $response = $this->get(route('clients.form'));

        // Assert: form is displayed
        $response->assertStatus(200);
        $response->assertViewIs('clients.form');
    }

    #[Test]
    public function it_displays_client_form_for_existing_client()
    {
        // Arrange: create a client
        $client = tmpClient::factory()->create(['client_name' => 'Test Client']);

        // Act: visit client edit form
        $response = $this->get(route('clients.view', ['client_id' => $client->client_id]));

        // Assert: form is displayed
        $response->assertStatus(200);
        $response->assertViewHas('client', function ($viewClient) use ($client) {
            return $viewClient->client_id === $client->client_id;
        });
        $response->assertSee('Test Client');
    }

    #[Test]
    public function it_redirects_when_cancel_button_is_clicked()
    {
        // Act: submit form with cancel button
        $response = $this->post(route('clients.form'), [
            'btn_cancel' => true,
        ]);

        // Assert: redirects to clients index
        $response->assertRedirect(route('clients.index'));
    }

    #[Test]
    public function it_displays_client_view()
    {
        // Arrange: create a client
        $client = tmpClient::factory()->create();

        // Act: visit client view page
        $response = $this->get(route('clients.view', ['client_id' => $client->id]));

        // Assert: view is displayed
        $response->assertStatus(200);
        $response->assertViewIs('clients.view');
        $response->assertViewHas('client');
        $response->assertSee($client->name);
    }

    #[Test]
    public function it_returns_404_for_non_existent_client()
    {
        // Act: visit view for non-existent client
        $response = $this->get(route('clients.view', ['client_id' => 99999]));

        // Assert: 404 error
        $response->assertStatus(404);
    }

    #[Test]
    public function it_deletes_client_and_redirects_to_index()
    {
        // Arrange: create a client
        $client = tmpClient::factory()->create();

        // Act: delete the client
        $response = $this->get(route('clients.delete', ['client_id' => $client->id]));

        // Assert: redirects and client is deleted
        $response->assertRedirect(route('clients.index'));
        $this->assertDatabaseMissing('ip_clients', ['client_id' => $client->id]);
    }
}

