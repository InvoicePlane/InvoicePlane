<?php

namespace Tests\Feature\Core;

use Dashboard;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(Dashboard::class)]
class DashboardTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function it_redirects_guests_to_the_login_page(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/dashboard');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET /dashboard must redirect. Got [%d].', $response->statusCode())
        );
    }

    #[Test]
    public function it_allows_authenticated_users_to_visit_the_dashboard(): void
    {
        /* Arrange */
        $this->actingAsAdmin();

        /* Act */
        $response = $this->get('/dashboard');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
    }
}
