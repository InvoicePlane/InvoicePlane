<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Sessions::logout() destroyed the session on a plain GET with no CSRF
 * check, so an attacker page could force an authenticated victim's session
 * to end simply by getting them to navigate to /sessions/logout (CWE-352).
 *
 * Logout is now a state-changing action like any other in this app: it
 * requires POST and a valid _csrf_field() token, matching the same
 * ensure_valid_post_request()-equivalent pattern Sessions already uses for
 * the new-password POST (Sessions is a Base_Controller, not an
 * Admin_Controller, so it mirrors the check inline rather than calling
 * ensure_valid_post_request()).
 *
 * The functional suite runs with CSRF_PROTECTION=false by default (see
 * .github/workflows/phpunit.yml), under which verify_csrf_token() early-
 * returns true and this gate is never exercised — the exact config-parity
 * trap config-parity-guard checks for. Every test here therefore explicitly
 * enables CSRF_PROTECTION to exercise the production configuration.
 *
 * GHSA-wj32-69hp-233w: Logout CSRF via tokenless GET /sessions/logout
 */
final class LogoutCsrfTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    /**
     * Deliberately does NOT call enableCsrfProtection(): with CSRF_PROTECTION off (the
     * functional suite's default), verify_csrf_token() short-circuits true for any request, so
     * this isolates the method check from the token check. Without the explicit "must be POST"
     * gate, a bare GET would destroy the session the moment CSRF is disabled -- exactly the
     * config a real deployment might run with, intentionally or not.
     */
    #[Test]
    public function it_does_not_destroy_the_session_for_a_plain_get_request_even_with_csrf_protection_disabled(): void
    {
        /* Arrange: simulating a forced top-level cross-site navigation to the logout link */

        /* Act */
        $response = $this->get('/sessions/logout');

        /* Assert: the session survives -- a GET is never enough to log out, regardless of CSRF_PROTECTION */
        self::assertTrue(
            $response->sessionActive(),
            'A bare GET to /sessions/logout must not destroy the session, even with CSRF protection disabled.'
        );
    }

    #[Test]
    public function it_does_not_destroy_the_session_for_a_post_without_a_valid_csrf_token(): void
    {
        /* Arrange: a forged cross-site POST can't read the victim's CSRF cookie */
        $this->enableCsrfProtection();

        /* Act */
        $response = $this->postWithoutCsrfToken('/sessions/logout');

        /* Assert: the session survives -- the request is a no-op, not a forced logout */
        self::assertTrue(
            $response->sessionActive(),
            'A POST to /sessions/logout without a matching CSRF token must not destroy the session.'
        );
    }

    /**
     * A present-but-wrong token. Verified by inspection (DebugLogoutTest, removed after use) that
     * CodeIgniter's own global csrf_verify() already rejects this case with a 403 before
     * Sessions::logout() runs at all -- for a non-excluded URI like this one, logout()'s own
     * verify_csrf_token() call is defense-in-depth, not the primary gate, the same as every other
     * CSRF-checked POST in this app. Kept as its own test because the user-facing contract
     * ("a mismatching token never logs out") must hold regardless of which layer enforces it.
     */
    #[Test]
    public function it_does_not_destroy_the_session_for_a_post_with_a_mismatching_csrf_token(): void
    {
        /* Arrange: a submitted token that does not match the victim's real CSRF cookie */
        $this->enableCsrfProtection();

        /* Act */
        $response = $this->post('/sessions/logout', ['_ip_csrf' => 'forged-token-0123456789-abcdefghi'], [], ['ip_csrf_cookie' => 'logout-csrf-token-0123456789-abcdef']);

        /* Assert: the session survives -- a present-but-wrong token must not destroy it either */
        self::assertTrue(
            $response->sessionActive(),
            'A POST to /sessions/logout with a mismatching CSRF token must not destroy the session.'
        );
    }

    #[Test]
    public function it_destroys_the_session_for_a_post_with_a_valid_csrf_token(): void
    {
        /* Arrange: the real logout form embeds the token via _csrf_field(), matching the CSRF cookie */
        $this->enableCsrfProtection();

        /* Act */
        $response = $this->postWithValidCsrfToken('/sessions/logout');

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'sessions/login');
        self::assertFalse(
            $response->sessionActive(),
            'A genuine logout POST with a valid CSRF token must still destroy the session.'
        );
    }
}
