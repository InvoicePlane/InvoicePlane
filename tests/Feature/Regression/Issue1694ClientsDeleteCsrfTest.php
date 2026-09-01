<?php

namespace Tests\Feature\Regression;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * #1694 regression — Controller: Clients::delete() (application/modules/clients).
 *
 * Guarded by Admin_Controller::ensure_valid_post_request(); the same
 * CSRF-token-consumed-by-bootstrap defect that broke invoice deletion broke
 * this action too. Proven both ways: a CSRF-valid POST deletes the client, a
 * token-less POST is refused by the framework and the client survives.
 */
#[Group('security')]
class Issue1694ClientsDeleteCsrfTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function it_deletes_a_client_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Issue 1694 Client']);

        /* Act */
        $response = $this->postWithValidCsrfToken('/clients/delete/' . $clientId);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('clients/delete must redirect. Got [%d].', $response->statusCode())
        );
        $this->assertDatabaseMissing('ip_clients', ['client_id' => $clientId]);
    }

    #[Test]
    public function it_rejects_the_delete_without_a_csrf_token(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Issue 1694 Client No Token']);

        /* Act */
        $response = $this->postWithoutCsrfToken('/clients/delete/' . $clientId);

        /* Assert */
        self::assertGreaterThanOrEqual(400, $response->statusCode());
        $this->assertDatabaseHas('ip_clients', ['client_id' => $clientId]);
    }
}
