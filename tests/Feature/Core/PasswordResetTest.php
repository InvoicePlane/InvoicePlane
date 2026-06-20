<?php

namespace Tests\Feature\Core;

use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Tests\Feature\Core\PasswordReset::class)]
class PasswordResetTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Requires Laravel service layer — not available in CI3');
    }
    use InteractsWithDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $this->markTestIncomplete('weak test');
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        $this->markTestIncomplete('weak test');
        Notification::fake();

        $user = $this->seedModel('User');

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        $this->markTestIncomplete('weak test');
        Notification::fake();

        $user = $this->seedModel('User');

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification): true {
            $response = $this->get('/reset-password/' . $notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = $this->seedModel('User');

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user): true {
            $response = $this->post('/reset-password', [
                'token'                 => $notification->token,
                'email'                 => $user->email,
                'password'              => 'password',
                'password_confirmation' => 'password',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    }
}
