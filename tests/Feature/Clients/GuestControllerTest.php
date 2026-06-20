<?php

namespace Tests\Feature\Clients;

use Modules\Crm\Controllers\ClientsController;
use Modules\Crm\Models\Client;
use Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;

/**
 * ClientsController Deletion Validation Feature Tests.
 *
 * Tests HTTP endpoints for client deletion with business rules:
 * - Clients with invoices, quotes, or projects cannot be deleted
 */
#[CoversClass(ClientsController::class)]
#[CoversClass(Tests\Feature\Clients\GuestController::class)]

class GuestControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Requires Laravel service layer — not available in CI3');
    }
    use InteractsWithDatabase;

    /**
     * Test index displays guest portal home page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_guest_portal_home_page(): void
    {
        /* Arrange */
        // Guest portal may not require authentication

        /* Act */
        $response = $this->get(route('guest.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_index');
    }

    /**
     * Test guest portal is accessible without authentication.
     */
    #[Test]
    public function it_is_accessible_without_authentication(): void
    {
        /* Arrange */
        // No authentication

        /* Act */
        $response = $this->get(route('guest.index'));

        /* Assert */
        $response->assertOk();
    }

    /**
     * Test guest portal is also accessible when authenticated.
     */
    #[Test]
    public function it_is_accessible_when_authenticated(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('guest.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_index');
    }
}
