<?php

namespace Tests\Feature\Core;

use Modules\Core\Models\User;
use Tests\Concerns\InteractsWithDatabase;
use Tests\TestCase;

class SessionsControllerTest extends TestCase
{
    use InteractsWithDatabase;

    use WithFaker;

    #[Test]
    public function it_redirects_index_to_login(): void
    {
        $response = $this->get(route('sessions.index'));

        $response->assertRedirect(route('sessions.login'));
    }

    #[Test]
    public function it_displays_login_page(): void
    {
        $response = $this->get(route('sessions.login'));

        $response->assertSuccessful();
        $response->assertViewIs('session_login');
        $response->assertViewHas('login_logo');
    }

    #[Test]
    public function it_authenticates_user_with_valid_credentials(): void
    {
        $user = $this->seedModel('User', [
            'user_email'    => 'test@example.com',
            'user_password' => Hash::make('password123'),
            'user_active'   => 1,
            'user_type'     => 1,
        ]);

        $response = $this->post(route('sessions.login'), [
            'btn_login' => true,
            'email'     => 'test@example.com',
            'password'  => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function it_redirects_guest_users_to_guest_area(): void
    {
        $user = $this->seedModel('User', [
            'user_email'    => 'guest@example.com',
            'user_password' => Hash::make('password123'),
            'user_active'   => 1,
            'user_type'     => 2, // Guest user
        ]);

        $response = $this->post(route('sessions.login'), [
            'btn_login' => true,
            'email'     => 'guest@example.com',
            'password'  => 'password123',
        ]);

        $response->assertRedirect(route('guest'));
    }

    #[Test]
    public function it_rejects_authentication_with_invalid_credentials(): void
    {
        $this->seedModel('User', [
            'user_email'    => 'test@example.com',
            'user_password' => Hash::make('password123'),
            'user_active'   => 1,
        ]);

        $response = $this->post(route('sessions.login'), [
            'btn_login' => true,
            'email'     => 'test@example.com',
            'password'  => 'wrongpassword',
        ]);

        $response->assertRedirect(route('sessions.login'));
        $response->assertSessionHas('alert_error');
        $this->assertGuest();
    }

    #[Test]
    public function it_rejects_authentication_for_nonexistent_user(): void
    {
        $response = $this->post(route('sessions.login'), [
            'btn_login' => true,
            'email'     => 'nonexistent@example.com',
            'password'  => 'password123',
        ]);

        $response->assertRedirect(route('sessions.login'));
        $response->assertSessionHas('alert_error', trans('loginalert_user_not_found'));
        $this->assertGuest();
    }

    #[Test]
    public function it_rejects_authentication_for_inactive_user(): void
    {
        $this->seedModel('User', [
            'user_email'    => 'inactive@example.com',
            'user_password' => Hash::make('password123'),
            'user_active'   => 0,
        ]);

        $response = $this->post(route('sessions.login'), [
            'btn_login' => true,
            'email'     => 'inactive@example.com',
            'password'  => 'password123',
        ]);

        $response->assertRedirect(route('sessions.login'));
        $response->assertSessionHas('alert_error', trans('loginalert_user_inactive'));
        $this->assertGuest();
    }

    #[Test]
    public function it_throttles_login_attempts_after_multiple_failures(): void
    {
        $user = $this->seedModel('User', [
            'user_email'    => 'test@example.com',
            'user_password' => Hash::make('password123'),
            'user_active'   => 1,
        ]);

        // Attempt 10 failed logins
        for ($i = 0; $i < 10; $i++) {
            $this->post(route('sessions.login'), [
                'btn_login' => true,
                'email'     => 'test@example.com',
                'password'  => 'wrongpassword',
            ]);
        }

        // 11th attempt should be blocked
        $response = $this->post(route('sessions.login'), [
            'btn_login' => true,
            'email'     => 'test@example.com',
            'password'  => 'password123',
        ]);

        $this->assertDatabaseHas('ip_login_log', [
            'login_name' => 'test@example.com',
        ]);
        $this->assertGuest();
    }

    #[Test]
    public function it_logs_out_authenticated_user(): void
    {
        $user = $this->seedModel('User', ['user_active' => 1]);
        $this->actingAs($user);

        $response = $this->get(route('sessions.logout'));

        $response->assertRedirect(route('sessions.login'));
        $this->assertGuest();
    }

    #[Test]
    public function it_displays_password_reset_page(): void
    {
        $response = $this->get(route('sessions.passwordreset'));

        $response->assertSuccessful();
        $response->assertViewIs('session_passwordreset');
    }

    #[Test]
    public function it_sends_password_reset_email(): void
    {
        Mail::fake();

        $user = $this->seedModel('User', [
            'user_email'  => 'test@example.com',
            'user_active' => 1,
        ]);

        $response = $this->post(route('sessions.passwordreset'), [
            'btn_reset' => true,
            'email'     => 'test@example.com',
        ]);

        $response->assertRedirect(route('sessions.login'));
        $response->assertSessionHas('alert_success');

        $user->refresh();
        $this->assertNotNull($user->user_passwordreset_token);
    }

    #[Test]
    public function it_validates_email_format_in_password_reset(): void
    {
        $response = $this->post(route('sessions.passwordreset'), [
            'btn_reset' => true,
            'email'     => 'invalid-email',
        ]);

        $response->assertRedirect('/');
    }

    #[Test]
    public function it_throttles_password_reset_attempts(): void
    {
        $user = $this->seedModel('User', [
            'user_email'  => 'test@example.com',
            'user_active' => 1,
        ]);

        // Attempt 10 password resets
        for ($i = 0; $i < 10; $i++) {
            $this->post(route('sessions.passwordreset'), [
                'btn_reset' => true,
                'email'     => 'test@example.com',
            ]);
        }

        $this->assertDatabaseHas('ip_login_log', [
            'login_name' => 'test@example.com',
        ]);
    }

    #[Test]
    public function it_displays_new_password_form_with_valid_token(): void
    {
        $user = $this->seedModel('User', [
            'user_email'               => 'test@example.com',
            'user_passwordreset_token' => 'valid_token_123',
            'user_active'              => 1,
        ]);

        $response = $this->get(route('sessions.passwordreset', ['token' => 'valid_token_123']));

        $response->assertSuccessful();
        $response->assertViewIs('session_new_password');
        $response->assertViewHas('token', 'valid_token_123');
        $response->assertViewHas('user_id', $user->user_id);
    }

    #[Test]
    public function it_rejects_invalid_password_reset_token(): void
    {
        $response = $this->get(route('sessions.passwordreset', ['token' => 'invalid_token']));

        $response->assertRedirect(route('sessions.passwordreset'));
        $response->assertSessionHas('alert_error');
    }

    #[Test]
    public function it_rejects_non_alphanumeric_token(): void
    {
        $response = $this->get(route('sessions.passwordreset', ['token' => 'token<script>alert(1)</script>']));

        $response->assertRedirect('/');
    }

    #[Test]
    public function it_updates_password_with_valid_token(): void
    {
        $user = $this->seedModel('User', [
            'user_email'               => 'test@example.com',
            'user_passwordreset_token' => 'valid_token_123',
            'user_active'              => 1,
        ]);

        $response = $this->post(route('sessions.passwordreset'), [
            'btn_new_password' => true,
            'token'            => 'valid_token_123',
            'user_id'          => $user->user_id,
            'new_password'     => 'newpassword123',
        ]);

        $response->assertRedirect(route('sessions.login'));

        $user->refresh();
        $this->assertEmpty($user->user_passwordreset_token);
        $this->assertTrue(Hash::check('newpassword123', $user->user_password));
    }

    #[Test]
    public function it_rejects_password_update_with_mismatched_token(): void
    {
        $user = $this->seedModel('User', [
            'user_email'               => 'test@example.com',
            'user_passwordreset_token' => 'valid_token_123',
            'user_active'              => 1,
        ]);

        $response = $this->post(route('sessions.passwordreset'), [
            'btn_new_password' => true,
            'token'            => 'wrong_token',
            'user_id'          => $user->user_id,
            'new_password'     => 'newpassword123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('alert_error');

        $user->refresh();
        $this->assertEquals('valid_token_123', $user->user_passwordreset_token);
    }

    #[Test]
    public function it_rejects_empty_password_in_reset(): void
    {
        $user = $this->seedModel('User', [
            'user_email'               => 'test@example.com',
            'user_passwordreset_token' => 'valid_token_123',
            'user_active'              => 1,
        ]);

        $response = $this->post(route('sessions.passwordreset'), [
            'btn_new_password' => true,
            'token'            => 'valid_token_123',
            'user_id'          => $user->user_id,
            'new_password'     => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('alert_error');
    }

    #[Test]
    public function it_clears_login_failures_after_successful_authentication(): void
    {
        $user = $this->seedModel('User', [
            'user_email'    => 'test@example.com',
            'user_password' => Hash::make('password123'),
            'user_active'   => 1,
            'user_type'     => 1,
        ]);

        // Create some failed attempts
        for ($i = 0; $i < 3; $i++) {
            $this->post(route('sessions.login'), [
                'btn_login' => true,
                'email'     => 'test@example.com',
                'password'  => 'wrongpassword',
            ]);
        }

        // Successful login
        $this->post(route('sessions.login'), [
            'btn_login' => true,
            'email'     => 'test@example.com',
            'password'  => 'password123',
        ]);

        $this->assertDatabaseMissing('ip_login_log', [
            'login_name' => 'test@example.com',
        ]);
    }

    #[Test]
    public function it_unlocks_account_after_12_hours(): void
    {
        $user = $this->seedModel('User', [
            'user_email'    => 'test@example.com',
            'user_password' => Hash::make('password123'),
            'user_active'   => 1,
            'user_type'     => 1,
        ]);

        // Create login log with old timestamp
        DB::table('ip_login_log')->insert([
            'login_name'           => 'test@example.com',
            'log_count'            => 11,
            'log_create_timestamp' => now()->subHours(13)->toDateTimeString(),
        ]);

        $response = $this->post(route('sessions.login'), [
            'btn_login' => true,
            'email'     => 'test@example.com',
            'password'  => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }
}
