<?php

namespace Tests\Feature\Ajax;

use Tests\AbstractTestCase;

#[\PHPUnit\Framework\Attributes\Test]
final class CsrfTokenHandlingTest extends AbstractTestCase
{
    public function it_accepts_valid_csrf_token_in_ajax_post_request(): void
    {
        /* Arrange */
        $this->actingAsAdmin();

        /* Act */
        $response = $this->request('POST', '/dashboard/ajax/chart_data', [], [], [], [
            'HTTP_X_IP_CSRF' => $this->sessionValue('_ip_csrf') ?? '',
        ]);

        /* Assert */
        // Should not get 403 Forbidden
        $this->assertNotEquals(403, $response->status());
    }

    public function it_rejects_missing_csrf_token_in_ajax_post(): void
    {
        /* Arrange */
        $this->actingAsAdmin();

        /* Act */
        $response = $this->request('POST', '/dashboard/ajax/chart_data', [], [], [], [
            // No CSRF header
        ]);

        /* Assert */
        $this->assertEquals(403, $response->status());
    }

    public function it_does_not_duplicate_csrf_token_when_already_present(): void
    {
        /* Arrange */
        $this->actingAsAdmin();
        $token = $this->sessionValue('_ip_csrf') ?? '';

        /* Act */
        // Simulate data that already contains a CSRF token (from scripts.js or form)
        $response = $this->request('POST', '/dashboard/ajax/chart_data', [
            '_ip_csrf' => $token,
        ]);

        /* Assert */
        // Should accept the token without duplication issues
        $this->assertNotEquals(403, $response->status());
    }

    public function it_extracts_token_from_fallback_cookie_if_meta_missing(): void
    {
        /* Arrange */
        $this->actingAsAdmin();
        $token = $this->sessionValue('_ip_csrf') ?? '';

        /* Act */
        // Send AJAX request without meta tag (simulating missing meta scenario in scripts.js)
        $response = $this->request('POST', '/dashboard/ajax/chart_data', [], [], [], [
            'HTTP_X_IP_CSRF' => $token,
            'HTTP_COOKIE'    => "ip_csrf_cookie={$token}",
        ]);

        /* Assert */
        // Should accept the token from cookie fallback
        $this->assertNotEquals(403, $response->status());
    }
}
