<?php

namespace Tests\Feature\Core;

use Dashboard;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Dashboard::class)]
class DashboardTest extends AbstractTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Requires live CI3 environment with database — not available in CI');
    }

    use InteractsWithDatabase;

    #[Test]
    public function it_redirects_guests_to_the_login_page(): void
    {
        /* Arrange */
        /* (no setup needed - unauthenticated request) */

        /* Act */
        $response = $this->get('/dashboard');

        /* Assert */
        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_allows_authenticated_users_to_visit_the_dashboard(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/dashboard');

        /* Assert */
        $response->assertStatus(200);
    }
}
