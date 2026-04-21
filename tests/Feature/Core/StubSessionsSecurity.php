<?php

namespace Tests\Feature\Core;

use DateTime;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

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

        return array_values(array_filter($timestamps, fn (int $ts) => $ts > $cutoff));
    }

    public function isRateLimited(array $attempts, int $maxAttempts, int $windowSeconds): bool
    {
        $active = $this->filterAttemptsWithinWindow($attempts, $windowSeconds);

        return count($active) >= $maxAttempts;
    }
}
