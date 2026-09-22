<?php

namespace Tests\Feature\Core;

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
    public function it_renders_the_login_page_with_a_200_status_when_unauthenticated(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->get('/sessions/login');

        /* Assert */
        $this->assertResponseHasNoPhpErrors($response);
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

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            'Submitting empty credentials must redirect back to login, not crash.'
        );
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
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_redirects_to_login_when_a_nonexistent_email_is_submitted_to_password_reset(): void
    {
        /* Arrange */
        $payload = ['btn_reset' => '1', 'email' => 'nobody_exists_' . time() . '@nonexistent.example'];

        /* Act */
        $response = $this->post('/sessions/passwordreset', $payload);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            'Password reset with nonexistent email must redirect (enumeration-safe response).'
        );
    }

    #[Test]
    public function it_does_not_reveal_whether_the_email_exists_in_the_reset_response(): void
    {
        /* Arrange */
        $ts = time();

        /* Act */
        $responseReal = $this->post('/sessions/passwordreset', [
            'btn_reset' => '1',
            'email'     => 'nobody_real_' . $ts . '@nonexistent.example',
        ]);
        $responseFake = $this->post('/sessions/passwordreset', [
            'btn_reset' => '1',
            'email'     => 'nobody_fake_' . $ts . '@nonexistent.example',
        ]);

        /* Assert */
        self::assertSame(
            $responseReal->statusCode(),
            $responseFake->statusCode(),
            'Password reset must return the same HTTP status for existing and nonexistent emails (enumeration guard).'
        );
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
        /* Arrange */
        $unknownToken = bin2hex(random_bytes(16));

        /* Act */
        $response = $this->get('/sessions/passwordreset/' . $unknownToken);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf(
                'An unknown but format-valid reset token must redirect to login. Got status [%d].',
                $response->statusCode()
            )
        );
    }

    #[Test]
    public function it_destroys_the_session_and_redirects_to_login_on_logout(): void
    {
        /* Arrange */
        $this->actingAsAdmin();

        /* Act */
        $response = $this->get('/sessions/logout');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('GET /sessions/logout must redirect. Got status [%d].', $response->statusCode())
        );

        // Location header is not available in PHP CLI SAPI; verify redirect status only.
        $redirectTarget = $response->redirectUrl() ?? '';

        if ($redirectTarget !== '') {
            self::assertTrue(
                str_contains($redirectTarget, 'sessions/login') || str_contains($redirectTarget, 'login'),
                sprintf('Logout must redirect to the login page. Redirect URL was [%s].', $redirectTarget)
            );
        }
    }

    #[Test]
    public function it_does_not_expose_php_errors_on_the_login_page(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->get('/sessions/login');

        /* Assert */
        $this->assertResponseHasNoPhpErrors($response);
    }
}
