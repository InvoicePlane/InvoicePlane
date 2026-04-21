<?php

namespace Modules\Crm\Tests\Feature;

use Modules\Crm\Controllers\ClientsController;
use Modules\Crm\Models\Client;
use Modules\Invoices\Models\Invoice;
use Modules\Projects\Models\Project;
use Modules\Quotes\Models\Quote;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\FeatureTestCase;

/**
 * ClientsController Deletion Validation Feature Tests.
 *
 * Tests HTTP endpoints for client deletion with business rules:
 * - Clients with invoices, quotes, or projects cannot be deleted
 */
#[CoversClass(ClientsController::class)]

class GuestControllerTest extends FeatureTestCase
{
    /**
     * Test index displays guest portal home page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_guest_portal_home_page(): void
    {
        /** Arrange */
        // Guest portal may not require authentication

        /** Act */
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
        /** Arrange */
        // No authentication

        /** Act */
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
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('guest.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_index');
    }
}
