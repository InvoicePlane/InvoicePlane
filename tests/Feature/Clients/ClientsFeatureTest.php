<?php

namespace Tests\Feature\Clients;

use Clients;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(Clients::class)]
class ClientsFeatureTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    #[Test]
    public function it_lists_active_clients(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Listed Corp', 'client_active' => 1]);

        /* Act */
        $response = $this->get('/clients/status/active');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertDatabaseHas('ip_clients', ['client_id' => $clientId, 'client_name' => 'Listed Corp']);
        $this->assertResponseBodyContains($response, '<html');
    }

    #[Test]
    public function it_lists_inactive_clients(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Dormant Inc', 'client_active' => 0]);

        /* Act */
        $response = $this->get('/clients/status/inactive');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertDatabaseHas('ip_clients', ['client_id' => $clientId, 'client_name' => 'Dormant Inc']);
        $this->assertResponseBodyContains($response, '<html');
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_create_client_form(): void
    {
        /* Arrange */
        /* (admin session set in setUp) */

        /* Act */
        $response = $this->get('/clients/form');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
    }

    #[Test]
    public function it_creates_a_client(): void
    {
        /**
         * POST /clients/form
         * {
         *     "client_name": "Acme Corp",
         *     "btn_submit": "1",
         *     "is_update": "0"
         * }
         */

        /* Arrange */
        /* (admin session set in setUp) */

        /* Act */
        $response = $this->post('/clients/form', [
            'client_name' => 'Acme Corp',
            'btn_submit'  => '1',
            'is_update'   => '0',
        ]);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            'Expected redirect but got HTTP ' . $response->statusCode() . '. Body: ' . mb_substr($response->body(), 0, 800)
        );
        $this->assertDatabaseHas('ip_clients', ['client_name' => 'Acme Corp']);
    }

    #[Test]
    public function it_creates_a_client_with_full_details(): void
    {
        /**
         * POST /clients/form
         * {
         *     "client_name": "Full Details Ltd",
         *     "client_surname": "Smith",
         *     "client_email": "full@example.com",
         *     "client_phone": "+31612345678",
         *     "client_city": "Amsterdam",
         *     "client_country": "NL",
         *     "btn_submit": "1",
         *     "is_update": "0"
         * }
         */

        /* Arrange */
        /* (admin session set in setUp) */

        /* Act */
        $response = $this->post('/clients/form', [
            'client_name'    => 'Full Details Ltd',
            'client_surname' => 'Smith',
            'client_email'   => 'full@example.com',
            'client_phone'   => '+31612345678',
            'client_city'    => 'Amsterdam',
            'client_country' => 'NL',
            'btn_submit'     => '1',
            'is_update'      => '0',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful create must redirect.');
        $this->assertDatabaseHas('ip_clients', [
            'client_name'  => 'Full Details Ltd',
            'client_email' => 'full@example.com',
            'client_city'  => 'Amsterdam',
        ]);
    }

    // -------------------------------------------------------------------------
    // Update
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_edit_form_showing_existing_client_name(): void
    {
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
        /**
         * POST /clients/form/{id}
         * {
         *     "client_name": "Renamed Corp",
         *     "btn_submit": "1",
         *     "is_update": "1"
         * }
         */

        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Original Name']);

        /* Act */
        $response = $this->post('/clients/form/' . $clientId, [
            'client_name' => 'Renamed Corp',
            'btn_submit'  => '1',
            'is_update'   => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful update must redirect.');
        $this->assertDatabaseHas('ip_clients', ['client_id' => $clientId, 'client_name' => 'Renamed Corp']);
        $this->assertDatabaseMissing('ip_clients', ['client_id' => $clientId, 'client_name' => 'Original Name']);
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_client(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Deletable Corp']);
        $this->assertDatabaseHas('ip_clients', ['client_id' => $clientId]);

        /* Act */
        $response = $this->post('/clients/delete/' . $clientId, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Delete must redirect.');
        $this->assertDatabaseMissing('ip_clients', ['client_id' => $clientId]);
    }

    // -------------------------------------------------------------------------
    // Validation failures — missing required fields
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_client_name(): void
    {
        /**
         * POST /clients/form
         * {
         *     "client_name": "",
         *     "btn_submit": "1",
         *     "is_update": "0"
         * }
         */

        /* Arrange */
        /* (admin session set in setUp) */

        /* Act */
        $response = $this->post('/clients/form', [
            'client_name' => '',
            'btn_submit'  => '1',
            'is_update'   => '0',
        ]);

        /* Assert */
        // Validation failure in CI3 re-renders the form at 200
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
    }

    #[Test]
    public function it_fails_to_update_without_client_name(): void
    {
        /**
         * POST /clients/form/{id}
         * {
         *     "client_name": "",
         *     "btn_submit": "1",
         *     "is_update": "1"
         * }
         */

        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Will Not Change']);

        /* Act */
        $response = $this->post('/clients/form/' . $clientId, [
            'client_name' => '',
            'btn_submit'  => '1',
            'is_update'   => '1',
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
        /* Arrange */
        /* (admin session set in setUp) */

        /* Act */
        $response = $this->get('/clients/view/999999999');

        /* Assert */
        $this->assertResponseBodyContains($response, 'Not Found');
    }

    #[Test]
    public function it_redirects_when_creating_a_duplicate_client(): void
    {
        /**
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
            'btn_submit'  => '1',
            'is_update'   => '0',
        ]);

        /* Assert */
        // CI3 controller redirects back with a flash error when the client already exists
        self::assertTrue($response->isRedirect(), 'Creating a duplicate client must redirect.');
    }

    #[Test]
    public function it_redirects_index_to_active_client_list(): void
    {
        /* Arrange */
        /* (admin session set in setUp) */

        /* Act */
        $response = $this->get('/clients');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'GET /clients must redirect to status/active.');
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
        $response = $this->get('/clients/status/active');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
    }
}
