<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Feature\Core;

use DateTime;
use DateTimeZone;

/**
 * Unit tests for Sessions controller security helpers.
 *
 * Extracted and tested in isolation — no CI3 bootstrap required.
 *
 * Covered:
 *  - _get_safe_referer() open-redirect guard
 *  - token format validation (alphanumeric-only guard)
 *  - password reset token expiry arithmetic
 *  - bot user-agent detection
 *  - IP-based rate-limit window filtering
 *  - MAX_PASSWORD_RESET_EXPIRY_MINUTES enforcement
 *
 * @group unit
 * @group security
 * @group sessions
 */
#[CoversClass(Tests\Feature\Core\SessionsSecurity::class)]
class SessionsSecurityTest extends AbstractTestCase
{
    private StubSessionsSecurity $security;

    protected function setUp(): void
    {
        parent::setUp();
        $this->security = new StubSessionsSecurity(baseUrl: 'https://invoiceplane.example.com/');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_allows_a_referer_from_the_same_base_url(): void
    {
        $result = $this->security->getSafeReferer('https://invoiceplane.example.com/sessions/login');

        self::assertSame(
            'https://invoiceplane.example.com/sessions/login',
            $result,
            'A referer from the same domain must be returned as-is.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_a_referer_from_an_external_domain(): void
    {
        $result = $this->security->getSafeReferer('https://evil.example.com/steal');

        self::assertSame(
            'sessions/passwordreset',
            $result,
            'An external referer must be replaced by the safe default.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_the_safe_default_when_referer_is_empty(): void
    {
        $result = $this->security->getSafeReferer('');

        self::assertSame(
            'sessions/passwordreset',
            $result,
            'An empty referer must return the safe default URL.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_a_referer_that_starts_with_a_double_slash(): void
    {
        $result = $this->security->getSafeReferer('//evil.example.com/steal');

        self::assertSame(
            'sessions/passwordreset',
            $result,
            'A protocol-relative referer to an external domain must be rejected.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_accepts_an_alphanumeric_password_reset_token(): void
    {
        self::assertTrue(
            $this->security->isValidTokenFormat('abc123XYZ'),
            'A purely alphanumeric token must pass format validation.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_accepts_a_hex_token_of_typical_length(): void
    {
        $token = bin2hex(random_bytes(16));

        self::assertTrue(
            $this->security->isValidTokenFormat($token),
            'A 32-character hex token (typical reset token) must pass format validation.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_a_token_containing_a_path_traversal_sequence(): void
    {
        self::assertFalse(
            $this->security->isValidTokenFormat('../etc/passwd'),
            'A token containing [../] must fail format validation.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_a_token_containing_a_slash(): void
    {
        self::assertFalse(
            $this->security->isValidTokenFormat('valid/invalid'),
            'A token containing a forward slash must fail format validation.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_a_token_containing_special_characters(): void
    {
        self::assertFalse(
            $this->security->isValidTokenFormat('token<script>'),
            'A token containing special characters must fail format validation.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_considers_an_expired_token_as_expired(): void
    {
        $expiry = new DateTime('-1 minute', new DateTimeZone('UTC'));

        self::assertTrue(
            $this->security->isTokenExpired($expiry->format('Y-m-d H:i:s')),
            'A token with an expiry timestamp in the past must be considered expired.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_considers_a_future_token_as_not_expired(): void
    {
        $expiry = new DateTime('+15 minutes', new DateTimeZone('UTC'));

        self::assertFalse(
            $this->security->isTokenExpired($expiry->format('Y-m-d H:i:s')),
            'A token with an expiry timestamp in the future must NOT be considered expired.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_enforces_the_max_expiry_minutes_cap_of_1440(): void
    {
        $requested = $this->security->clampExpiryMinutes(9999);

        self::assertSame(
            15,
            $requested,
            'An out-of-range PASSWORD_RESET_TOKEN_EXPIRY_MINUTES must fall back to the 15-minute default.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_allows_a_valid_expiry_minutes_value_within_range(): void
    {
        $result = $this->security->clampExpiryMinutes(30);

        self::assertSame(
            30,
            $result,
            'A value of 30 minutes is within 1-1440 and must be returned unchanged.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_a_zero_expiry_minutes_and_falls_back_to_default(): void
    {
        $result = $this->security->clampExpiryMinutes(0);

        self::assertSame(
            15,
            $result,
            'An expiry_minutes value of 0 is invalid and must fall back to the 15-minute default.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_detects_curl_as_a_bot_user_agent(): void
    {
        self::assertTrue(
            $this->security->isBotUserAgent('curl/7.85.0'),
            'curl must be identified as a bot/automated tool.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_detects_python_requests_as_a_bot_user_agent(): void
    {
        self::assertTrue(
            $this->security->isBotUserAgent('python-requests/2.28.0'),
            'python-requests must be identified as a bot.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_detects_an_empty_user_agent_as_a_bot(): void
    {
        self::assertTrue(
            $this->security->isBotUserAgent(''),
            'An empty user-agent must be treated as a bot/automated request.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_not_flag_a_normal_browser_user_agent_as_a_bot(): void
    {
        $browser = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36';

        self::assertFalse(
            $this->security->isBotUserAgent($browser),
            'A standard browser user-agent must NOT be flagged as a bot.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_removes_attempts_outside_the_rate_limit_time_window(): void
    {
        $now        = time();
        $windowSecs = 3600;

        $attempts = [
            $now - 7200,
            $now - 5000,
            $now - 1800,
            $now - 100,
            $now - 30,
        ];

        $filtered = $this->security->filterAttemptsWithinWindow($attempts, $windowSecs);

        self::assertCount(
            3,
            $filtered,
            'Only attempts within the last 3600 seconds must be retained.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_considers_the_ip_rate_limited_when_attempt_count_meets_the_threshold(): void
    {
        $now      = time();
        $attempts = array_fill(0, 5, $now - 10);

        $isLimited = $this->security->isRateLimited(attempts: $attempts, maxAttempts: 5, windowSeconds: 3600);

        self::assertTrue(
            $isLimited,
            'Exactly 5 attempts against a max of 5 must trigger the rate limit.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_not_rate_limit_when_attempt_count_is_below_the_threshold(): void
    {
        $now      = time();
        $attempts = array_fill(0, 4, $now - 10);

        $isLimited = $this->security->isRateLimited(attempts: $attempts, maxAttempts: 5, windowSeconds: 3600);

        self::assertFalse(
            $isLimited,
            '4 attempts against a max of 5 must NOT trigger the rate limit.'
        );
    }
}

class StubSessionsSecurity
{
    private const MAX_EXPIRY_MINUTES = 1440;

    private const BOT_SIGNATURES = [
        'curl', 'wget', 'python-requests', 'go-http-client',
        'java/', 'apache-httpclient', 'okhttp', 'httpclient',
        'bot', 'spider', 'crawler', 'scraper',
        'postman', 'insomnia', 'paw/',
    ];

    public function __construct(private readonly string $baseUrl) {}

    public function getSafeReferer(string $referer): string
    {
        if (empty($referer)) {
            return 'sessions/passwordreset';
        }

        if (str_starts_with($referer, $this->baseUrl)) {
            return $referer;
        }

        return 'sessions/passwordreset';
    }

    public function isValidTokenFormat(string $token): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9\-_]+$/', $token);
    }

    public function isTokenExpired(string $expiryTimestamp): bool
    {
        $utc    = new DateTimeZone('UTC');
        $expiry = new DateTime($expiryTimestamp, $utc);
        $now    = new DateTime('now', $utc);

        return $now > $expiry;
    }

    public function clampExpiryMinutes(int $minutes): int
    {
        if ($minutes < 1 || $minutes > self::MAX_EXPIRY_MINUTES) {
            return 15;
        }

        return $minutes;
    }

    public function isBotUserAgent(string $userAgent): bool
    {
        if (empty($userAgent)) {
            return true;
        }

        $lower = mb_strtolower($userAgent);

        foreach (self::BOT_SIGNATURES as $sig) {
            if (str_contains($lower, $sig)) {
                return true;
            }
        }

        return false;
    }

    public function filterAttemptsWithinWindow(array $timestamps, int $windowSeconds): array
    {
        $cutoff = time() - $windowSeconds;

        return array_values(array_filter($timestamps, fn (int $ts): bool => $ts > $cutoff));
    }

    public function isRateLimited(array $attempts, int $maxAttempts, int $windowSeconds): bool
    {
        $active = $this->filterAttemptsWithinWindow($attempts, $windowSeconds);

        return count($active) >= $maxAttempts;
    }
}
