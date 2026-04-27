<?php

namespace Tests\Feature\Clients;

use Get;
use Modules\Crm\Controllers\ClientsController;
use Modules\Crm\Models\Client;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

/**
 * ClientsController Deletion Validation Feature Tests.
 *
 * Tests HTTP endpoints for client deletion with business rules:
 * - Clients with invoices, quotes, or projects cannot be deleted
 */
#[CoversClass(Get::class)]
#[CoversClass(Tests\Feature\Clients\GetController::class)]

class GetControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    /**
     * Test index displays guest get page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_guest_get_page(): void
    {
        $this->markTestIncomplete('Only accessible with Guest Url and special key');
        /* Arrange */
        // Guest operations may not require authentication

        /* Act */
        $response = $this->get('/guest/get');

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_get');
    }

    /**
     * Test guest get page is accessible without authentication.
     */
    #[Test]
    public function it_is_accessible_without_authentication(): void
    {
        $this->markTestIncomplete('Only accessible with Guest Url and special key');
        /* Arrange */
        // No authentication required

        /* Act */
        $response = $this->get('/guest/get');

        /* Assert */
        $response->assertOk();
    }

    /**
     * Test guest get page is also accessible when authenticated.
     */
    #[Test]
    public function it_is_accessible_when_authenticated(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/guest/get');

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_get');
    }
}
