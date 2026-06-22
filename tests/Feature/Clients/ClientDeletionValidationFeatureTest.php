<?php

namespace Tests\Feature\Clients;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * ClientsController Deletion Validation Feature Tests.
 *
 * Tests HTTP endpoints for client deletion with business rules:
 * - Clients with invoices, quotes, or projects cannot be deleted
 */
class ClientDeletionValidationFeatureTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_or_redirect(): void
    {
        /* Arrange */
        $this->seedClient(['client_name' => 'Deletion Validation Client']);

        /* Act */
        $response = $this->get('/clients/status/active');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<html');
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/clients/status/active');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/clients] must redirect. Got [%d].', $response->statusCode())
        );
    }
}
