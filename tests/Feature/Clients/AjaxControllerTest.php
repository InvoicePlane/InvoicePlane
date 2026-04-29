<?php

namespace Tests\Feature\Clients;

use Ajax;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Ajax::class)]

class AjaxControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_returns_clients_matching_name_query(): void
    {
        /* Arrange */
        $this->seedModel('Client', ['client_name' => 'Test Client']);

        /* Act */
        $response = $this->get('/clients/ajax/name_query', ['query' => 'Test']);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $json = $response->json();
        self::assertIsArray($json);
        self::assertNotEmpty($json, 'Expected at least one client matching "Test".');
    }

    #[Test]
    public function it_gets_latest_clients(): void
    {
        /* Arrange */
        $this->seedModel('Client');

        /* Act */
        $response = $this->get('/clients/ajax/get_latest');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
    }

    #[Test]
    public function it_saves_permissive_search_preference(): void
    {
        /* Act */
        $response = $this->get('/clients/ajax/save_preference_permissive_search_clients', [
            'permissive_search_clients' => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
    }

    #[Test]
    public function it_deletes_client_note(): void
    {
        /* Arrange */
        $clientId = $this->seedModel('Client')->client_id;
        $note     = $this->seedModel('ClientNote', ['client_id' => $clientId]);

        /* Act */
        $response = $this->post('/clients/ajax/delete_client_note', [
            'client_note_id' => $note->client_note_id,
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertDatabaseMissing('ip_client_notes', ['client_note_id' => $note->client_note_id]);
    }

    #[Test]
    public function it_saves_client_note(): void
    {
        /* Arrange */
        $clientId = $this->seedModel('Client')->client_id;

        /* Act */
        $response = $this->post('/clients/ajax/save_client_note', [
            'client_id'   => $clientId,
            'client_note' => 'This is a test note',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertDatabaseHas('ip_client_notes', [
            'client_id'  => $clientId,
            'client_note' => 'This is a test note',
        ]);
    }

    #[Test]
    public function it_loads_client_notes(): void
    {
        /* Arrange */
        $clientId = $this->seedModel('Client')->client_id;
        $note     = $this->seedModel('ClientNote', ['client_id' => $clientId]);

        /* Act */
        $response = $this->post('/clients/ajax/load_client_notes', [
            'client_id' => $clientId,
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, $note->client_note);
    }
}
