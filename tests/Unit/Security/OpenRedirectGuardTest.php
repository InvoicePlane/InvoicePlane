<?php

namespace Tests\Unit\Security;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the _get_safe_referer() open-redirect guard.
 *
 * The fix replaced str_starts_with($referer, $base_url) with a parse_url()
 * host comparison, and added an early return when base_url is empty.
 *
 * Covered:
 *  - Empty base_url → always returns safe default (not every referer accepted)
 *  - Same-host referer → accepted
 *  - External domain → rejected, safe default returned
 *  - Subdomain-of-base-url trick (evil.base.com) → rejected
 *  - Protocol-relative URL (//evil.com/...) → rejected
 *  - Null/empty referer → safe default
 *  - Subdirectory confusion (base_url = https://example.com/app/) → host matched correctly
 */
#[Group('unit')]
#[Group('security')]
class OpenRedirectGuardTest extends TestCase
{
    private const SAFE_DEFAULT = 'sessions/passwordreset';

    private function guard(string $baseUrl): StubOpenRedirectGuard
    {
        return new StubOpenRedirectGuard($baseUrl);
    }

    #[Test]
    public function it_returns_safe_default_when_base_url_is_empty(): void
    {
        /* Arrange */
        $guard = $this->guard('');

        /* Act */
        $result = $guard->getSafeReferer('https://example.com/sessions/login');

        /* Assert */
        self::assertSame(
            self::SAFE_DEFAULT,
            $result,
            'When base_url is empty, any referer must be rejected to prevent open redirect.'
        );
    }

    #[Test]
    public function it_accepts_a_referer_from_the_same_host(): void
    {
        /* Arrange */
        $guard = $this->guard('https://invoiceplane.example.com/');

        /* Act */
        $result = $guard->getSafeReferer('https://invoiceplane.example.com/invoices/index');

        /* Assert */
        self::assertSame(
            'https://invoiceplane.example.com/invoices/index',
            $result,
            'A referer whose host matches the base_url host must be returned as-is.'
        );
    }

    #[Test]
    public function it_rejects_a_referer_from_an_external_domain(): void
    {
        /* Arrange */
        $guard = $this->guard('https://invoiceplane.example.com/');

        /* Act */
        $result = $guard->getSafeReferer('https://evil.com/steal');

        /* Assert */
        self::assertSame(self::SAFE_DEFAULT, $result);
    }

    #[Test]
    public function it_rejects_a_subdomain_that_ends_with_the_base_host(): void
    {
        /* Arrange */
        // Attacker registers evil.invoiceplane.example.com — str_starts_with check would pass
        // if base_url is https://invoiceplane.example.com (no trailing slash check).
        // The parse_url host comparison must reject it.
        $guard = $this->guard('https://invoiceplane.example.com/');

        /* Act */
        $result = $guard->getSafeReferer('https://evil.invoiceplane.example.com/steal');

        /* Assert */
        self::assertSame(
            self::SAFE_DEFAULT,
            $result,
            'A subdomain of the base host must be rejected (host must match exactly).'
        );
    }

    #[Test]
    public function it_rejects_a_domain_that_contains_the_base_host_as_a_substring(): void
    {
        /* Arrange */
        // Attacker registers invoiceplane.example.com.evil.net
        $guard = $this->guard('https://invoiceplane.example.com/');

        /* Act */
        $result = $guard->getSafeReferer('https://invoiceplane.example.com.evil.net/steal');

        /* Assert */
        self::assertSame(
            self::SAFE_DEFAULT,
            $result,
            'A domain that contains the base host as a substring must be rejected.'
        );
    }

    #[Test]
    public function it_rejects_a_protocol_relative_url(): void
    {
        /* Arrange */
        $guard = $this->guard('https://invoiceplane.example.com/');

        /* Act */
        $result = $guard->getSafeReferer('//evil.com/steal');

        /* Assert */
        self::assertSame(
            self::SAFE_DEFAULT,
            $result,
            'A protocol-relative URL to an external domain must be rejected.'
        );
    }

    #[Test]
    public function it_returns_safe_default_for_an_empty_referer(): void
    {
        /* Arrange */
        $guard = $this->guard('https://invoiceplane.example.com/');

        /* Act */
        $result = $guard->getSafeReferer('');

        /* Assert */
        self::assertSame(self::SAFE_DEFAULT, $result);
    }

    #[Test]
    public function it_accepts_a_referer_when_base_url_has_a_subdirectory_path(): void
    {
        /* Arrange */
        // Installed at https://example.com/billing/ — host is still example.com
        $guard = $this->guard('https://example.com/billing/');

        /* Act */
        $result = $guard->getSafeReferer('https://example.com/billing/invoices');

        /* Assert */
        self::assertSame(
            'https://example.com/billing/invoices',
            $result,
            'A same-host referer must be accepted even when base_url contains a subdirectory.'
        );
    }

    #[Test]
    #[DataProvider('bypassAttempts')]
    public function it_rejects_known_open_redirect_bypass_patterns(string $referer, string $reason): void
    {
        /* Arrange */
        $guard = $this->guard('https://invoiceplane.example.com/');

        /* Act */
        $result = $guard->getSafeReferer($referer);

        /* Assert */
        self::assertSame(
            self::SAFE_DEFAULT,
            $result,
            "Bypass attempt [{$reason}] must be rejected. Got: [{$result}]"
        );
    }

    public static function bypassAttempts(): array
    {
        return [
            'javascript scheme'              => ['javascript:alert(1)', 'JavaScript XSS via referer'],
            'data URI'                       => ['data:text/html,<script>evil()</script>', 'data: URI'],
            'at-sign host confusion'         => ['https://invoiceplane.example.com@evil.com/', '@-sign host confusion'],
            'whitespace-prefixed external'   => ['  https://evil.com/steal', 'leading whitespace'],
        ];
    }
}

/**
 * Stub that mirrors the fixed _get_safe_referer() logic from Sessions.php.
 */
class StubOpenRedirectGuard
{
    private const SAFE_DEFAULT = 'sessions/passwordreset';

    public function __construct(private readonly string $baseUrl) {}

    public function getSafeReferer(string $referer): string
    {
        $referer = trim($referer);

        if (empty($referer)) {
            return self::SAFE_DEFAULT;
        }

        if (empty($this->baseUrl)) {
            return self::SAFE_DEFAULT;
        }

        $refererHost = parse_url($referer, PHP_URL_HOST);
        $baseHost    = parse_url($this->baseUrl, PHP_URL_HOST);

        if ( ! $refererHost || ! $baseHost || $refererHost !== $baseHost) {
            return self::SAFE_DEFAULT;
        }

        return $referer;
    }
}
