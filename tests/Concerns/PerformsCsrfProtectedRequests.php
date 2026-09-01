<?php

namespace Tests\Concerns;

use Tests\Integration\Support\HttpResponse;

/**
 * Helpers for exercising a route with CodeIgniter CSRF protection actually
 * enabled.
 *
 * The functional suite runs with CSRF_PROTECTION=false (see the PHPUnit CI
 * workflow), so verify_csrf_token() early-returns and the controller-level
 * re-check is never exercised. A test class that needs the real CSRF pathway
 * calls enableCsrfProtection() in setUp() to opt back in — the same approach
 * SecurityRegressionTest uses for the generate_pdf CSRF gate — and then drives
 * requests through postWithValidCsrfToken() / postWithoutCsrfToken().
 */
trait PerformsCsrfProtectedRequests
{
    private string $csrfToken = 'issue-1694-csrf-token-0123456789';

    /**
     * Turn CodeIgniter's global CSRF protection on for this test class.
     */
    protected function enableCsrfProtection(): void
    {
        $this->withEnvironment(['CSRF_PROTECTION' => 'true']);
    }

    /**
     * POST carrying a matching CSRF token field and cookie, so CodeIgniter's
     * bootstrap Security::csrf_verify() accepts the request and hands control
     * to the controller (which is where #1694 then bounced it).
     *
     * @param array<string, mixed> $data
     */
    protected function postWithValidCsrfToken(string $uri, array $data = []): HttpResponse
    {
        return $this->post(
            $uri,
            $data + ['_ip_csrf' => $this->csrfToken],
            [],
            ['ip_csrf_cookie' => $this->csrfToken]
        );
    }

    /**
     * POST with no CSRF token at all. CodeIgniter rejects it during bootstrap,
     * before any controller code runs.
     *
     * @param array<string, mixed> $data
     */
    protected function postWithoutCsrfToken(string $uri, array $data = []): HttpResponse
    {
        return $this->post($uri, $data);
    }
}
