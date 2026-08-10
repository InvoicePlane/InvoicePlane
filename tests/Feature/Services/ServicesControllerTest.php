<?php

namespace Tests\Feature\Services;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class ServicesControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        $this->setUpDatabase();
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_lists_services(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_services', [
            'service_name' => 'Listed Consulting',
        ]);

        /* Act */
        $response = $this->get('/services');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'Listed Consulting');
        $this->assertDatabaseHas('ip_services', ['service_name' => 'Listed Consulting']);
    }

    #[Test]
    public function it_renders_the_create_form(): void
    {
        /* Act */
        $response = $this->get('/services/form');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertResponseBodyContains($response, 'service_name');
    }

    #[Test]
    public function it_creates_a_service(): void
    {
        /* Act */
        $response = $this->post('/services/form', [
            'service_name' => 'Implementation',
            'btn_submit'   => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful service create must redirect.');
        $this->assertDatabaseHas('ip_services', ['service_name' => 'Implementation']);
    }

    #[Test]
    public function it_updates_a_service(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_services', [
            'service_name' => 'Original Service',
        ]);

        /* Act */
        $response = $this->post('/services/form/' . $id, [
            'service_name' => 'Renamed Service',
            'btn_submit'   => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful service update must redirect.');
        $this->assertDatabaseHas('ip_services', ['service_id' => $id, 'service_name' => 'Renamed Service']);
        $this->assertDatabaseMissing('ip_services', ['service_id' => $id, 'service_name' => 'Original Service']);
    }

    #[Test]
    public function it_does_not_create_a_service_without_a_name(): void
    {
        /* Act */
        $response = $this->post('/services/form', [
            'service_name' => '',
            'btn_submit'   => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertDatabaseMissing('ip_services', ['service_name' => '']);
    }

    #[Test]
    public function it_deletes_a_service_and_its_client_assignments(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Service Client']);
        $serviceId = $this->databaseInsert('ip_services', [
            'service_name' => 'Deletable Service',
        ]);
        $this->databaseInsert('ip_client_services', [
            'client_id'  => $clientId,
            'service_id' => $serviceId,
        ]);

        /* Act */
        $response = $this->post('/services/delete/' . $serviceId);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Service delete must redirect.');
        $this->assertDatabaseMissing('ip_services', ['service_id' => $serviceId]);
        $this->assertDatabaseMissing('ip_client_services', [
            'client_id'  => $clientId,
            'service_id' => $serviceId,
        ]);
    }

    #[Test]
    public function it_assigns_a_created_service_to_a_client(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Assigned Client']);

        /* Act */
        $response = $this->post('/services/form_client/' . $clientId, [
            'service_name' => 'Client Service',
            'btn_submit'   => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Client service create must redirect.');
        $service = $this->databaseFetchOne('ip_services', ['service_name' => 'Client Service']);
        self::assertNotNull($service);
        $this->assertDatabaseHas('ip_client_services', [
            'client_id'  => $clientId,
            'service_id' => $service['service_id'],
        ]);
    }

    #[Test]
    public function it_rejects_client_service_creation_for_a_missing_client(): void
    {
        /* Act */
        $response = $this->post('/services/form_client/999999', [
            'service_name' => 'Orphan Service',
            'btn_submit'   => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
        $this->assertDatabaseMissing('ip_services', ['service_name' => 'Orphan Service']);
    }

    #[Test]
    public function it_returns_404_for_invalid_service_ids(): void
    {
        /* Act */
        $formResponse = $this->get('/services/form/not-a-number');
        $deleteResponse = $this->post('/services/delete/not-a-number');

        /* Assert */
        $this->assertResponseStatusCode($formResponse, 404);
        $this->assertResponseStatusCode($deleteResponse, 404);
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/services');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
    }
}
