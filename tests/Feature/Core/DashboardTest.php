<?php

namespace Tests\Feature\Core;

use Tests\Concerns\InteractsWithDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use InteractsWithDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $this->actingAs($user = $this->seedModel('User'));

        $this->get('/dashboard')->assertStatus(200);
    }
}
