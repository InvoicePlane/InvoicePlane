<?php

namespace Tests\Unit\Clients;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Smoke test for the ClientNotesServiceTest module via CI3 HTTP harness.
 */
class ClientNotesServiceTest extends AbstractTestCase
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
        $this->seedClient(['client_name' => 'Notes Service Client Eta']);

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
