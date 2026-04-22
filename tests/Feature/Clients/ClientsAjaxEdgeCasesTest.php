<?php

namespace Tests\Feature\Clients;

use Modules\Crm\Controllers\ClientsController;
use Modules\Crm\Models\Client;
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

class ClientsAjaxEdgeCasesTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    // ==================== VALIDATION & EDGE CASES ====================

    /**
     * Test getClientDetails with invalid ID type.
     */
    #[Group('validation')]
    #[Test]
    public function it_handles_invalid_client_id_type(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('crm.ajax.get_client_details', ['clientId' => 'invalid']));

        /* Assert */
        // Should either return 404 or handle gracefully
        $this->assertTrue(
            $response->isNotFound()
            || $response->getStatusCode() >= 400
        );
    }

    /**
     * Test getClientDetails with negative ID.
     */
    #[Group('validation')]
    #[Test]
    public function it_handles_negative_client_id(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('crm.ajax.get_client_details', ['clientId' => -1]));

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test getClientDetails with zero ID.
     */
    #[Group('validation')]
    #[Test]
    public function it_handles_zero_client_id(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('crm.ajax.get_client_details', ['clientId' => 0]));

        /* Assert */
        $response->assertNotFound();
    }
}
