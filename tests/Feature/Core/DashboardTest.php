<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
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
