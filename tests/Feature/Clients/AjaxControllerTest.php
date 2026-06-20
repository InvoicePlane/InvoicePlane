<?php

namespace Tests\Feature\Clients;

use Ajax;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Ajax::class)]
#[CoversClass(Tests\Feature\Clients\AjaxController::class)]

class AjaxControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Requires Laravel service layer — not available in CI3');
    }
    use InteractsWithDatabase;

    #[Test]
    public function it_returns_clients_matching_name_query(): void
    {
        /* Arrange */
        $client = $this->seedModel('\Modules\Clients\Models\tmpClient', ['name' => 'Test Client']);

        /**
         * Payload:
         * {
         *   "query": "Test"
         * }
         */
        /* Act */
        $response = $this->json('POST', route('clients.ajax.nameQuery'), [
            'query' => 'Test',
        ]);

        /* Assert */
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Test Client']);
    }

    #[Test]
    public function it_gets_latest_clients(): void
    {
        /* Arrange */
        $client = $this->seedModel('\Modules\Clients\Models\tmpClient');

        /* Act */
        $response = $this->get(route('clients.ajax.getLatest'));

        /* Assert */
        $response->assertStatus(200);
    }

    #[Test]
    public function it_saves_permissive_search_preference(): void
    {
        /**
         * Payload:
         * {
         *   "permissive_search_clients": "1"
         * }
         */
        /* Act */
        $response = $this->json('POST', route('clients.ajax.savePreference'), [
            'permissive_search_clients' => '1',
        ]);

        /* Assert */
        $response->assertStatus(200);
    }

    #[Test]
    public function it_deletes_client_note(): void
    {
        /* Arrange */
        $client = $this->seedModel('\Modules\Clients\Models\tmpClient');
        $note   = $this->seedModel('\Modules\Crm\app\Models\ClientNote', ['client_id' => $client->id]);

        /* Act */
        $response = $this->json('POST', route('clients.ajax.deleteNote', ['note_id' => $note->id]));

        /* Assert */
        $response->assertStatus(200);
        $this->assertDatabaseMissing('ip_client_notes', ['id' => $note->id]);
    }

    #[Test]
    public function it_saves_client_note(): void
    {
        /* Arrange */
        $client = $this->seedModel('\Modules\Clients\Models\tmpClient');

        /**
         * Payload:
         * {
         *   "client_id": 1,
         *   "note": "This is a test note"
         * }
         */
        /* Act */
        $response = $this->json('POST', route('clients.ajax.saveNote'), [
            'client_id' => $client->id,
            'note'      => 'This is a test note',
        ]);

        /* Assert */
        $response->assertStatus(200);
        $this->assertDatabaseHas('ip_client_notes', [
            'client_id' => $client->id,
            'note'      => 'This is a test note',
        ]);
    }

    #[Test]
    public function it_loads_client_notes(): void
    {
        /* Arrange */
        $client = $this->seedModel('\Modules\Clients\Models\tmpClient');
        $note   = $this->seedModel('\Modules\Crm\app\Models\ClientNote', ['client_id' => $client->id]);

        /* Act */
        $response = $this->get(route('clients.ajax.loadNotes', ['client_id' => $client->id]));

        /* Assert */
        $response->assertStatus(200);
        $response->assertSee($note->note);
    }
}
