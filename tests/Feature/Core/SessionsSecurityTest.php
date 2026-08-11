<?php

namespace Tests\Feature\Core;

use DateTime;
use DateTimeZone;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

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
 */
#[Group('unit')]
#[Group('security')]
#[Group('sessions')]
class SessionsSecurityTest extends AbstractTestCase
{
    private StubSessionsSecurity $security;

    protected function setUp(): void
    {
        parent::setUp();
        $this->security = new StubSessionsSecurity(baseUrl: 'https://invoiceplane.example.com/');
    }

    /**
     * @return array<string, array{0: string, 1: bool}>
     */
    public static function expiryFormatProvider(): array
    {
        return [
            // description => [stored expiry string, accepted?]
            'canonical timestamp'                  => ['2020-01-01 12:00:00', true],
            'canonical boundary timestamp'         => ['2099-12-31 23:59:59', true],
            'garbage time-only string'             => ['25:99:99', false],
            'out-of-range month and day'           => ['2020-13-40 00:00:00', false],
            'non-date string'                      => ['not-a-date', false],
            'non-canonical single-digit fields'    => ['2026-8-10 9:05:07', false],
            'non-canonical double space'           => ['2099-01-01  12:00:00', false],
            'zero date (right shape, unreal date)' => ['0000-00-00 00:00:00', false],
        ];
    }

    #[Test]
    public function it_allows_a_referer_from_the_same_base_url(): void
    {
        /* Arrange */

        /* Act */
        $result = $this->security->getSafeReferer('https://invoiceplane.example.com/sessions/login');

        /* Assert */
        self::assertSame(
            'https://invoiceplane.example.com/sessions/login',
            $result,
            'A referer from the same domain must be returned as-is.'
        );
    }

    #[Test]
    public function it_rejects_a_referer_from_an_external_domain(): void
    {
        /* Arrange */

        /* Act */
        $result = $this->security->getSafeReferer('https://evil.example.com/steal');

        /* Assert */
        self::assertSame(
            'sessions/passwordreset',
            $result,
            'An external referer must be replaced by the safe default.'
        );
    }

    #[Test]
    public function it_returns_the_safe_default_when_referer_is_empty(): void
    {
        /* Arrange */

        /* Act */
        $result = $this->security->getSafeReferer('');

        /* Assert */
        self::assertSame(
            'sessions/passwordreset',
            $result,
            'An empty referer must return the safe default URL.'
        );
    }

    #[Test]
    public function it_rejects_a_referer_that_starts_with_a_double_slash(): void
    {
        /* Arrange */

        /* Act */
        $result = $this->security->getSafeReferer('//evil.example.com/steal');

        /* Assert */
        self::assertSame(
            'sessions/passwordreset',
            $result,
            'A protocol-relative referer to an external domain must be rejected.'
        );
    }

    #[Test]
    public function it_accepts_an_alphanumeric_password_reset_token(): void
    {
        /* Arrange */

        /* Act */

        /* Assert */
        self::assertTrue(
            $this->security->isValidTokenFormat('abc123XYZ'),
            'A purely alphanumeric token must pass format validation.'
        );
    }

    #[Test]
    public function it_accepts_a_hex_token_of_typical_length(): void
    {
        /* Arrange */

        /* Act */
        $token = bin2hex(random_bytes(16));

        /* Assert */
        self::assertTrue(
            $this->security->isValidTokenFormat($token),
            'A 32-character hex token (typical reset token) must pass format validation.'
        );
    }

    #[Test]
    public function it_rejects_a_token_containing_a_path_traversal_sequence(): void
    {
        /* Arrange */

        /* Act */

        /* Assert */
        self::assertFalse(
            $this->security->isValidTokenFormat('../etc/passwd'),
            'A token containing [../] must fail format validation.'
        );
    }

    #[Test]
    public function it_rejects_a_token_containing_a_slash(): void
    {
        /* Arrange */

        /* Act */

        /* Assert */
        self::assertFalse(
            $this->security->isValidTokenFormat('valid/invalid'),
            'A token containing a forward slash must fail format validation.'
        );
    }

    #[Test]
    public function it_rejects_a_token_containing_special_characters(): void
    {
        /* Arrange */

        /* Act */

        /* Assert */
        self::assertFalse(
            $this->security->isValidTokenFormat('token<script>'),
            'A token containing special characters must fail format validation.'
        );
    }

    #[Test]
    public function it_considers_an_expired_token_as_expired(): void
    {
        /* Arrange */

        /* Act */
        $expiry = new DateTime('-1 minute', new DateTimeZone('UTC'));

        /* Assert */
        self::assertTrue(
            $this->security->isTokenExpired($expiry->format('Y-m-d H:i:s')),
            'A token with an expiry timestamp in the past must be considered expired.'
        );
    }

    #[Test]
    public function it_considers_a_future_token_as_not_expired(): void
    {
        /* Arrange */

        /* Act */
        $expiry = new DateTime('+15 minutes', new DateTimeZone('UTC'));

        /* Assert */
        self::assertFalse(
            $this->security->isTokenExpired($expiry->format('Y-m-d H:i:s')),
            'A token with an expiry timestamp in the future must NOT be considered expired.'
        );
    }

    #[Test]
    public function it_enforces_the_max_expiry_minutes_cap_of_1440(): void
    {
        /* Arrange */

        /* Act */
        $requested = $this->security->clampExpiryMinutes(9999);

        /* Assert */
        self::assertSame(
            15,
            $requested,
            'An out-of-range PASSWORD_RESET_TOKEN_EXPIRY_MINUTES must fall back to the 15-minute default.'
        );
    }

    #[Test]
    public function it_allows_a_valid_expiry_minutes_value_within_range(): void
    {
        /* Arrange */

        /* Act */
        $result = $this->security->clampExpiryMinutes(30);

        /* Assert */
        self::assertSame(
            30,
            $result,
            'A value of 30 minutes is within 1-1440 and must be returned unchanged.'
        );
    }

    #[Test]
    public function it_rejects_a_zero_expiry_minutes_and_falls_back_to_default(): void
    {
        /* Arrange */

        /* Act */
        $result = $this->security->clampExpiryMinutes(0);

        /* Assert */
        self::assertSame(
            15,
            $result,
            'An expiry_minutes value of 0 is invalid and must fall back to the 15-minute default.'
        );
    }

    #[Test]
    public function it_detects_curl_as_a_bot_user_agent(): void
    {
        /* Arrange */

        /* Act */

        /* Assert */
        self::assertTrue(
            $this->security->isBotUserAgent('curl/7.85.0'),
            'curl must be identified as a bot/automated tool.'
        );
    }

    #[Test]
    public function it_detects_python_requests_as_a_bot_user_agent(): void
    {
        /* Arrange */

        /* Act */

        /* Assert */
        self::assertTrue(
            $this->security->isBotUserAgent('python-requests/2.28.0'),
            'python-requests must be identified as a bot.'
        );
    }

    #[Test]
    public function it_detects_an_empty_user_agent_as_a_bot(): void
    {
        /* Arrange */

        /* Act */

        /* Assert */
        self::assertTrue(
            $this->security->isBotUserAgent(''),
            'An empty user-agent must be treated as a bot/automated request.'
        );
    }

    #[Test]
    public function it_does_not_flag_a_normal_browser_user_agent_as_a_bot(): void
    {
        /* Arrange */

        /* Act */
        $browser = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36';

        /* Assert */
        self::assertFalse(
            $this->security->isBotUserAgent($browser),
            'A standard browser user-agent must NOT be flagged as a bot.'
        );
    }

    #[Test]
    public function it_removes_attempts_outside_the_rate_limit_time_window(): void
    {
        /* Arrange */
        $now        = time();
        $windowSecs = 3600;

        $attempts = [
            $now - 7200,
            $now - 5000,
            $now - 1800,
            $now - 100,
            $now - 30,
        ];

        /* Act */
        $filtered = $this->security->filterAttemptsWithinWindow($attempts, $windowSecs);

        /* Assert */
        self::assertCount(
            3,
            $filtered,
            'Only attempts within the last 3600 seconds must be retained.'
        );
    }

    #[Test]
    public function it_considers_the_ip_rate_limited_when_attempt_count_meets_the_threshold(): void
    {
        /* Arrange */
        $now      = time();
        $attempts = array_fill(0, 5, $now - 10);

        /* Act */
        $isLimited = $this->security->isRateLimited(attempts: $attempts, maxAttempts: 5, windowSeconds: 3600);

        /* Assert */
        self::assertTrue(
            $isLimited,
            'Exactly 5 attempts against a max of 5 must trigger the rate limit.'
        );
    }

    #[Test]
    public function it_does_not_rate_limit_when_attempt_count_is_below_the_threshold(): void
    {
        /* Arrange */
        $now      = time();
        $attempts = array_fill(0, 4, $now - 10);

        /* Act */
        $isLimited = $this->security->isRateLimited(attempts: $attempts, maxAttempts: 5, windowSeconds: 3600);

        /* Assert */
        self::assertFalse(
            $isLimited,
            '4 attempts against a max of 5 must NOT trigger the rate limit.'
        );
    }

    #[Test]
    public function it_accepts_only_canonical_password_reset_expiry_strings(): void
    {
        foreach (self::expiryFormatProvider() as [$expiry, $accepted]) {
            /* Act */
            $result = $this->security->isCanonicalExpiry($expiry);

            /* Assert */
            self::assertSame(
                $accepted,
                $result,
                sprintf('Expiry string "%s" must be %s.', $expiry, $accepted ? 'accepted' : 'rejected')
            );
        }
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

    /**
     * Strict expiry parsing, mirroring Sessions::_reject_expired_password_reset_token().
     *
     * A stored expiry is accepted only when it matches the exact, anchored canonical
     * Y-m-d H:i:s shape and createFromFormat() parses it with no warnings/errors. This
     * rejects out-of-range values (25:99:99), rolled-over dates (2020-13-40 00:00:00), and
     * non-canonical spacing/single-digit fields (2026-8-10 9:05:07) that createFromFormat()
     * would otherwise normalize silently.
     */
    public function isCanonicalExpiry(string $raw): bool
    {
        if ( ! preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $raw)) {
            return false;
        }

        $utc          = new DateTimeZone('UTC');
        $parsed       = DateTime::createFromFormat('!Y-m-d H:i:s', $raw, $utc);
        $parse_errors = DateTime::getLastErrors();

        if ($parsed === false) {
            return false;
        }

        return ! ($parse_errors !== false
            && ($parse_errors['warning_count'] > 0 || $parse_errors['error_count'] > 0));
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
