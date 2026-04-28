<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Feature\Core;

use Dashboard;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Dashboard::class)]
class DashboardTest extends AbstractTestCase
{
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
