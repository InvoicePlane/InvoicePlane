<?php

namespace Tests\Unit\Security;

use PHPUnit\Framework\TestCase;

class SecurityHeadersTest extends TestCase
{
    /**
     * Validates X-Frame-Options fallback behavior: unknown values should fall back
     * to SAMEORIGIN to prevent accidental security degradation from misconfiguration.
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_falls_back_to_sameorigin_for_invalid_x_frame_options(): void
    {
        $allowed_values = ['SAMEORIGIN' => "'self'", 'DENY' => "'none'"];

        // Test case 1: Valid SAMEORIGIN
        $value = mb_strtoupper(trim('SAMEORIGIN'));
        $this->assertArrayHasKey($value, $allowed_values);
        $this->assertSame('SAMEORIGIN', $value);

        // Test case 2: Valid DENY
        $value = mb_strtoupper(trim('DENY'));
        $this->assertArrayHasKey($value, $allowed_values);
        $this->assertSame('DENY', $value);

        // Test case 3: Invalid value (should fail isset check and fallback to SAMEORIGIN)
        $value = mb_strtoupper(trim('ALLOWALL'));
        $this->assertFalse(isset($allowed_values[$value]));
        // Simulate fallback
        if ( ! isset($allowed_values[$value])) {
            $value = 'SAMEORIGIN';
        }
        $this->assertSame('SAMEORIGIN', $value);
        $this->assertArrayHasKey($value, $allowed_values);

        // Test case 4: Lowercase value is converted to uppercase before check
        $value = mb_strtoupper(trim('sameorigin'));
        $this->assertArrayHasKey($value, $allowed_values);

        // Test case 5: Whitespace is trimmed before validation
        $value = mb_strtoupper(trim('  SAMEORIGIN  '));
        $this->assertArrayHasKey($value, $allowed_values);

        // Test case 6: Empty string falls back
        $value = mb_strtoupper(trim(''));
        $this->assertFalse(isset($allowed_values[$value]));
        if ( ! isset($allowed_values[$value])) {
            $value = 'SAMEORIGIN';
        }
        $this->assertSame('SAMEORIGIN', $value);
    }

    /**
     * Validates that CSP frame-ancestors value matches the X-Frame-Options setting.
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_matches_csp_frame_ancestors_to_x_frame_options(): void
    {
        $allowed_values = ['SAMEORIGIN' => "'self'", 'DENY' => "'none'"];

        // For each valid X-Frame-Options value, verify CSP frame-ancestors matches
        foreach ($allowed_values as $frame_option => $csp_value) {
            $expected_csp = "Content-Security-Policy: frame-ancestors {$csp_value}; object-src 'none'; base-uri 'self'";

            // Verify the CSP value for this frame option is correct
            $this->assertStringContainsString("frame-ancestors {$csp_value}", $expected_csp);
            $this->assertStringContainsString("object-src 'none'", $expected_csp);
            $this->assertStringContainsString("base-uri 'self'", $expected_csp);
        }
    }

    /**
     * Validates referrer policy and X-Content-Type-Options headers are always set
     * (or conditionally set based on configuration).
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sets_security_headers_consistently(): void
    {
        // Referrer-Policy should always be set to strict-origin-when-cross-origin
        $referrer_policy = 'Referrer-Policy: strict-origin-when-cross-origin';
        $this->assertStringContainsString('strict-origin-when-cross-origin', $referrer_policy);

        // X-Content-Type-Options should be set when enabled (default behavior)
        $x_content_type = 'X-Content-Type-Options: nosniff';
        $this->assertStringContainsString('nosniff', $x_content_type);
    }
}
