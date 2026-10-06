<?php

namespace Tests\Feature\Core\Auth;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Feature tests for the Sessions module.
 *
 * Covers: login page rendering, credential rejection, logout redirect,
 * password-reset form, token validation guard, bot-detection guard,
 * and the email-enumeration-safe response shape.
 */
#[Group('feature')]
#[Group('sessions')]
class SessionsFeatureTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsGuest();
    }

    #[Test]
    public function it_includes_a_login_form_on_the_sessions_login_page(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->get('/sessions/login');

        /* Assert */

        self::assertTrue(
            $response->contains('email') || $response->contains('password'),
            'The login page must contain an email or password input field.'
        );
    }

    #[Test]
    public function it_does_not_render_the_admin_dashboard_when_unauthenticated(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->get('/dashboard');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf(
                'An unauthenticated GET /dashboard must redirect to login. Got status [%d].',
                $response->statusCode()
            )
        );
    }

    #[Test]
    public function it_redirects_to_login_when_post_credentials_are_missing(): void
    {
        /* Arrange */
        $payload = ['btn_login' => '1', 'email' => '', 'password' => ''];

        /* Act */
        $response = $this->post('/sessions/login', $payload);

        /* Assert: bounced back, and no counter was created for an empty (spoofable) identity */
        self::assertTrue($response->isRedirect(), 'Submitting empty credentials must redirect back to login, not crash.');
        $this->assertDatabaseCount('ip_login_log', 0);
    }

    #[Test]
    public function it_redirects_to_login_with_wrong_credentials(): void
    {
        /* Arrange */
        $payload = [
            'btn_login' => '1',
            'email'     => 'nobody@nonexistent.example',
            'password'  => 'wrongpassword',
        ];

        /* Act */
        $response = $this->post('/sessions/login', $payload);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            'Invalid credentials must redirect (not 200 with error, not 500).'
        );

        self::assertFalse(
            $response->contains('dashboard'),
            'A failed login must never redirect to the dashboard.'
        );
    }

    #[Test]
    public function it_renders_the_password_reset_form_with_a_200_status(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->get('/sessions/passwordreset');

        /* Assert */
        $this->assertResponseOk($response);
        $this->assertResponseBodyContains($response, 'id="password_reset"');
        $this->assertResponseBodyContains($response, 'btn_reset');
    }

    #[Test]
    public function it_redirects_to_login_when_a_nonexistent_email_is_submitted_to_password_reset(): void
    {
        /* Arrange */
        $payload = ['btn_reset' => '1', 'email' => 'nobody_exists_' . time() . '@nonexistent.example'];

        /* Act */
        $response = $this->post('/sessions/passwordreset', $payload);

        /* Assert: enumeration-safe redirect, and no reset token was issued to anyone */
        self::assertTrue($response->isRedirect(), 'Password reset with nonexistent email must redirect (enumeration-safe response).');
        self::assertSame(0, $this->usersWithResetToken());
    }

    #[Test]
    public function it_does_not_reveal_whether_the_email_exists_in_the_reset_response(): void
    {
        /* Arrange */
        $this->seedResetUser('real-user@test.local', 1);
        $this->seedResetUser('inactive-user@test.local', 0);

        /* Act */
        $real     = $this->post('/sessions/passwordreset', ['btn_reset' => '1', 'email' => 'real-user@test.local']);
        $fake     = $this->post('/sessions/passwordreset', ['btn_reset' => '1', 'email' => 'nobody_fake@nonexistent.example']);
        $inactive = $this->post('/sessions/passwordreset', ['btn_reset' => '1', 'email' => 'inactive-user@test.local']);

        /* Assert: identical status, but only the active account really received a token */
        self::assertSame($real->statusCode(), $fake->statusCode(), 'Existing and nonexistent emails must get the same HTTP status.');
        self::assertSame($real->statusCode(), $inactive->statusCode(), 'Inactive accounts must be indistinguishable too.');
        $realUser = $this->databaseFetchOne('ip_users', ['user_email' => 'real-user@test.local']);
        self::assertNotEmpty($realUser['user_passwordreset_token'], 'The active account must hold a (hashed) reset token.');
        self::assertSame(1, $this->usersWithResetToken(), 'Only the active, existing account may be issued a reset token.');
    }

    #[Test]
    public function it_rejects_a_password_reset_token_containing_non_alphanumeric_characters(): void
    {
        /* Arrange */
        $maliciousToken = '../etc/passwd';

        /* Act */
        $response = $this->get('/sessions/passwordreset/' . rawurlencode($maliciousToken));

        /* Assert */
        self::assertThat(
            $response->statusCode(),
            self::logicalOr(
                self::equalTo(302),
                self::equalTo(301),
                self::equalTo(307),
                self::equalTo(404)
            ),
            sprintf(
                'A non-alphanumeric reset token must be rejected with a redirect or 404. Got [%d].',
                $response->statusCode()
            )
        );

        $this->assertResponseBodyNotContains($response, 'etc/passwd');
        $this->assertResponseBodyNotContains($response, 'root:');
    }

    #[Test]
    public function it_redirects_to_login_when_an_unknown_valid_format_token_is_used(): void
    {
        /* Arrange: a real account with a pending reset token of its own */
        $userId = $this->seedResetUser('pending-reset@test.local', 1);
        $this->databaseUpdate('ip_users', ['user_passwordreset_token' => hash('sha256', 'the-real-token'), 'user_passwordreset_token_expiry' => gmdate('Y-m-d H:i:s', time() + 900)], ['user_id' => $userId]);
        $before = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);

        /* Act */
        $response = $this->get('/sessions/passwordreset/' . bin2hex(random_bytes(16)));

        /* Assert: bounced, and the other account's token and password are untouched */
        self::assertTrue($response->isRedirect(), sprintf('An unknown but format-valid reset token must redirect. Got [%d].', $response->statusCode()));
        $after = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        self::assertSame($before['user_passwordreset_token'], $after['user_passwordreset_token']);
        self::assertSame($before['user_password'], $after['user_password']);
    }

    #[Test]
    public function it_redirects_to_login_on_logout(): void
    {
        /* Arrange */
        $this->actingAsAdmin();
        $control = $this->get('/supplier_invoices');

        /* Act */
        $response = $this->get('/sessions/logout');

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'sessions/login');
        self::assertTrue($control->sessionActive(), 'Control: a normal authenticated request keeps its session open.');
        self::assertFalse($response->sessionActive(), 'Logout must destroy the session.');
    }

    private function seedResetUser(string $email, int $active): int
    {
        return $this->databaseInsert('ip_users', [
            'user_name'         => 'Reset Tester', 'user_email' => $email, 'user_type' => 1, 'user_active' => $active,
            'user_password'     => password_hash('correct-password', PASSWORD_BCRYPT), 'user_psalt' => bin2hex(random_bytes(10)),
            'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),
        ]);
    }

    private function usersWithResetToken(): int
    {
        return (int) $this->databaseSelect("SELECT COUNT(*) AS c FROM ip_users WHERE user_passwordreset_token IS NOT NULL AND user_passwordreset_token <> ''")[0]['c'];
    }
}
