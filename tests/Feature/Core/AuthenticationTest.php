<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Tests\Feature\Core\Authentication::class)]
class AuthenticationTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    #[Test]
    public function it_renders_the_login_screen(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/login');

        /* Assert */
        $response->assertStatus(200);
    }

    #[Test]
    public function it_authenticates_users_via_the_login_screen(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $response = $this->post('/login', [
            'email'    => $user->user_email,
            'password' => 'secret',
        ]);

        /* Assert */
        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    #[Test]
    public function it_rejects_authentication_with_invalid_password(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $this->post('/login', [
            'email'    => $user->user_email,
            'password' => 'wrong-password',
        ]);

        /* Assert */
        $this->assertGuest();
    }

    #[Test]
    public function it_logs_out_authenticated_users(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $response = $this->actingAs($user)->post('/logout');

        /* Assert */
        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
