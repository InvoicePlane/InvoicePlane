<?php

namespace Tests\Feature\Clients;

use Modules\Crm\Controllers\ClientsController;
use Modules\Crm\Models\Client;
use Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;
use View;

/**
 * ClientsController Deletion Validation Feature Tests.
 *
 * Tests HTTP endpoints for client deletion with business rules:
 * - Clients with invoices, quotes, or projects cannot be deleted
 */
#[CoversClass(View::class)]
#[CoversClass(Tests\Feature\Clients\ViewController::class)]

class ViewControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Requires Laravel service layer — not available in CI3');
    }
    use InteractsWithDatabase;

    /**
     * Test index displays guest view page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_guest_view_page(): void
    {
        $this->markTestIncomplete('Only accessible with Guest Url and special key');
        /* Arrange */
        // Guest operations may not require authentication

        /* Act */
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
        $this->markTestIncomplete('Only accessible with Guest Url and special key');
        /* Arrange */
        // No authentication required

        /* Act */
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
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('guest.view'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_view');
    }
}
