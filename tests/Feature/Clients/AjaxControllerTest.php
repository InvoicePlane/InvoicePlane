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
