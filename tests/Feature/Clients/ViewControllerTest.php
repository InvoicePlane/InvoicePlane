<?php

namespace Tests\Feature\Clients;

use Modules\Crm\Controllers\ClientsController;
use Modules\Crm\Models\Client;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;
use Tests\Feature\FeatureTestCase;

/**
 * ClientsController Deletion Validation Feature Tests.
 *
 * Tests HTTP endpoints for client deletion with business rules:
 * - Clients with invoices, quotes, or projects cannot be deleted
 */
#[CoversClass(ClientsController::class)]

class ViewControllerTest extends FeatureTestCase
{
    use InteractsWithDatabase;

    /**
     * Test index displays guest view page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_guest_view_page(): void
    {
        /** Arrange */
        // Guest operations may not require authentication

        /** Act */
        $response = $this->get(route('guest.view'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_view');
    }

    /**
     * Test guest view page is accessible without authentication.
     */
    #[Test]
    public function it_is_accessible_without_authentication(): void
    {
        /** Arrange */
        // No authentication required

        /** Act */
        $response = $this->get(route('guest.view'));

        /* Assert */
        $response->assertOk();
    }

    /**
     * Test guest view page is also accessible when authenticated.
     */
    #[Test]
    public function it_is_accessible_when_authenticated(): void
    {
        /** Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('guest.view'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_view');
    }
}
