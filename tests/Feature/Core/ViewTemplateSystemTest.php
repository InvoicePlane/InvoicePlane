<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * ViewTemplateSystem Feature Tests.
 *
 * Tests that the dashboard renders a full HTML page for authenticated admins.
 */
class ViewTemplateSystemTest extends AbstractTestCase
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
        /* (authenticated admin via setUp) */

        /* Act */
        $response = $this->get('/dashboard');

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
        $response = $this->get('/dashboard');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/dashboard] must redirect. Got [%d].', $response->statusCode())
        );
    }
}
