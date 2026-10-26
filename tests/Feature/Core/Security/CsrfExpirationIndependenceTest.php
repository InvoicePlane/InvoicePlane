<?php

namespace Tests\Feature\Core\Security;

use Tests\AbstractTestCase;

/**
 * CSRF cookie expiration independence tests.
 *
 * Verifies that CSRF cookie lifetime (csrf_expire) is independent of session
 * lifetime (SESS_EXPIRATION). With SESS_EXPIRATION=0 (session until browser
 * closes), the CSRF cookie should NOT get Max-Age=0 and be discarded immediately.
 *
 * Issue: https://github.com/InvoicePlane/InvoicePlane/issues/...
 * When SESS_EXPIRATION=0, csrf_expire was incorrectly set to 0, causing the
 * CSRF cookie to have Max-Age=0 and be dropped by the browser, making all
 * POST requests fail with 403 CSRF validation error.
 */
final class CsrfExpirationIndependenceTest extends AbstractTestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_issues_csrf_cookie_with_correct_max_age_when_sess_expiration_is_zero(): void
    {
        /* Arrange */
        $this->withEnvironment(['SESS_EXPIRATION' => '0']);

        /* Act */
        $response = $this->request('GET', '/sessions/login');

        /* Assert */
        $this->assertEquals(200, $response->status());

        $csrfCookie = $response->cookie('ip_csrf_cookie');
        $this->assertNotNull($csrfCookie, 'CSRF cookie should be set');

        $setCookieHeader = $response->header('Set-Cookie');
        $this->assertStringContainsString(
            'Max-Age=3600',
            $setCookieHeader,
            'CSRF cookie should have Max-Age=3600 (1 hour), independent of SESS_EXPIRATION=0'
        );

        $this->assertStringNotContainsString(
            'Max-Age=0',
            $setCookieHeader,
            'CSRF cookie must not have Max-Age=0 when SESS_EXPIRATION=0'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_allows_login_with_sess_expiration_zero(): void
    {
        /* Arrange */
        $this->withEnvironment(['SESS_EXPIRATION' => '0']);

        /* Act: Get login form and extract CSRF token */
        $loginResponse = $this->request('GET', '/sessions/login');
        preg_match(
            '/<input[^>]*name="' . preg_quote(config_item('csrf_token_name')) . '"[^>]*value="([^"]*)"/',
            $loginResponse->body(),
            $matches
        );
        $csrfToken = $matches[1] ?? null;
        $this->assertNotNull($csrfToken, 'CSRF token should be in login form');

        /* Post login with SESS_EXPIRATION=0 */
        $response = $this->request('POST', '/sessions/login', [
            'post' => [
                config_item('csrf_token_name') => $csrfToken,
                'email'                        => 'admin@test.local',
                'password'                     => 'password',
            ],
        ]);

        /* Assert */
        $this->assertIn(
            $response->status(),
            [200, 302, 303],
            'Login should succeed (200/302/303 redirect) when SESS_EXPIRATION=0, not 403 CSRF error'
        );

        /* Positive control: Also verify login works with normal SESS_EXPIRATION */
        $normalLoginResponse = $this->request('GET', '/sessions/login', [], ['SESS_EXPIRATION' => '864000']);
        preg_match(
            '/<input[^>]*name="' . preg_quote(config_item('csrf_token_name')) . '"[^>]*value="([^"]*)"/',
            $normalLoginResponse->body(),
            $normalMatches
        );
        $normalCsrfToken = $normalMatches[1] ?? null;

        $normalResponse = $this->request('POST', '/sessions/login', [
            'post' => [
                config_item('csrf_token_name') => $normalCsrfToken,
                'email'                        => 'admin@test.local',
                'password'                     => 'password',
            ],
        ]);

        $this->assertIn(
            $normalResponse->status(),
            [200, 302, 303],
            'Login should also succeed with normal SESS_EXPIRATION (positive control)'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_validates_csrf_token_independently_of_session_expiration(): void
    {
        /* Arrange */
        $this->actingAsAdmin();
        $this->withEnvironment(['SESS_EXPIRATION' => '0']);

        /* Act: POST with SESS_EXPIRATION=0 */
        $response = $this->postWithValidCsrfToken('/settings');

        /* Assert */
        $this->assertIn(
            $response->status(),
            [200, 302, 303, 307],
            'POST with valid CSRF token should succeed (200/302/303/307) when SESS_EXPIRATION=0, not 403'
        );

        /* Positive control: Also verify POST works with normal SESS_EXPIRATION */
        $this->actingAsAdmin();
        $this->withEnvironment(['SESS_EXPIRATION' => '864000']);

        $normalResponse = $this->postWithValidCsrfToken('/settings');

        $this->assertIn(
            $normalResponse->status(),
            [200, 302, 303, 307],
            'POST with valid CSRF token should also succeed with normal SESS_EXPIRATION (positive control)'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_posts_without_csrf_even_when_sess_expiration_is_zero(): void
    {
        /* Arrange */
        $this->actingAsAdmin();
        $this->withEnvironment(['SESS_EXPIRATION' => '0']);

        /* Act */
        $response = $this->request('POST', '/settings');

        /* Assert */
        $this->assertEquals(
            403,
            $response->status(),
            'POST without CSRF token should be rejected (403) regardless of SESS_EXPIRATION value'
        );
    }
}
