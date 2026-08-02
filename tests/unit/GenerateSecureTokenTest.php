<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Tests for generate_secure_token() in application/helpers/security_helper.php.
 *
 * These tokens (invoice_url_key, quote_url_key, cron_key) are the sole
 * access-control mechanism for unauthenticated access to sensitive financial
 * data, so they must be generated with a CSPRNG (CWE-338 / CWE-330).
 */
final class GenerateSecureTokenTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        // The helper file guards against direct access via BASEPATH and
        // conditionally requires the CodeIgniter core helper (skipped when the
        // path does not resolve), so it can be loaded in isolation here.
        if ( ! defined('BASEPATH')) {
            define('BASEPATH', '/nonexistent-basepath/');
        }

        require_once __DIR__ . '/../../application/helpers/security_helper.php';
    }

    #[Test]
    public function it_returns_a_token_of_the_requested_length(): void
    {
        $this->assertSame(32, strlen(generate_secure_token(32)));
        $this->assertSame(16, strlen(generate_secure_token(16)));
    }

    #[Test]
    public function it_defaults_to_a_32_character_token(): void
    {
        $this->assertSame(32, strlen(generate_secure_token()));
    }

    #[Test]
    public function it_handles_odd_lengths_exactly(): void
    {
        $this->assertSame(15, strlen(generate_secure_token(15)));
    }

    #[Test]
    public function it_falls_back_to_the_default_length_for_non_positive_input(): void
    {
        $this->assertSame(32, strlen(generate_secure_token(0)));
        $this->assertSame(32, strlen(generate_secure_token(-5)));
    }

    #[Test]
    public function it_only_contains_lowercase_hexadecimal_characters(): void
    {
        $token = generate_secure_token(64);

        $this->assertMatchesRegularExpression('/^[0-9a-f]+$/', $token);
    }

    #[Test]
    public function it_produces_unique_tokens(): void
    {
        $tokens = [];
        for ($i = 0; $i < 5000; $i++) {
            $tokens[generate_secure_token(32)] = true;
        }

        $this->assertCount(5000, $tokens);
    }
}
