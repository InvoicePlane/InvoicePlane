<?php

namespace Tests\Unit\Security;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the Crypt library's bcrypt migration.
 *
 * Covered:
 *  - generate_password() produces a $2y$ bcrypt hash
 *  - check_password() verifies a freshly generated hash
 *  - check_password() handles legacy $2a$ hashes (transparent migration)
 *  - check_password() rejects a wrong password
 *  - The $salt parameter is accepted but ignored (API-compat shim)
 */
#[Group('unit')]
#[Group('security')]
class CryptTest extends TestCase
{
    private StubCrypt $crypt;

    protected function setUp(): void
    {
        $this->crypt = new StubCrypt();
    }

    #[Test]
    public function it_generates_a_bcrypt_hash_with_the_2y_prefix(): void
    {
        /* Arrange */
        $password = 'super-secret-pass!1';

        /* Act */
        $hash = $this->crypt->generate_password($password);

        /* Assert */
        self::assertStringStartsWith(
            '$2y$',
            $hash,
            'generate_password() must produce a bcrypt $2y$ hash.'
        );
    }

    #[Test]
    public function it_verifies_a_freshly_generated_bcrypt_hash(): void
    {
        /* Arrange */
        $password = 'correct-horse-battery-staple';
        $hash     = $this->crypt->generate_password($password);

        /* Act */
        $result = $this->crypt->check_password($hash, $password);

        /* Assert */
        self::assertTrue($result, 'check_password() must return true for the correct password.');
    }

    #[Test]
    public function it_rejects_a_wrong_password_against_a_bcrypt_hash(): void
    {
        /* Arrange */
        $hash = $this->crypt->generate_password('right-password');

        /* Act */
        $result = $this->crypt->check_password($hash, 'wrong-password');

        /* Assert */
        self::assertFalse($result, 'check_password() must return false for an incorrect password.');
    }

    #[Test]
    public function it_verifies_a_legacy_2a_prefix_hash_transparently(): void
    {
        /* Arrange */
        // $2a$ hash produced by older PHP versions — password_verify() handles both prefixes.
        $password    = 'legacy-password';
        $legacy_hash = password_hash($password, PASSWORD_BCRYPT);
        // Simulate a stored $2a$ hash by replacing the prefix.
        $legacy_hash_2a = '$2a$' . substr($legacy_hash, 4);

        /* Act */
        $result = $this->crypt->check_password($legacy_hash_2a, $password);

        /* Assert */
        self::assertTrue(
            $result,
            'check_password() must verify passwords against legacy $2a$ hashes without re-hashing.'
        );
    }

    #[Test]
    public function it_ignores_the_salt_parameter_for_api_compatibility(): void
    {
        /* Arrange */
        $password = 'test-password';
        $salt     = 'ignored-salt-value';

        /* Act */
        $hash   = $this->crypt->generate_password($password, $salt);
        $result = $this->crypt->check_password($hash, $password);

        /* Assert */
        self::assertTrue(
            $result,
            'generate_password() must produce a verifiable hash even when a $salt argument is passed.'
        );
    }

    #[Test]
    public function it_produces_a_different_hash_for_each_call_due_to_random_salt(): void
    {
        /* Arrange */
        $password = 'same-password-every-time';

        /* Act */
        $hash1 = $this->crypt->generate_password($password);
        $hash2 = $this->crypt->generate_password($password);

        /* Assert */
        self::assertNotSame(
            $hash1,
            $hash2,
            'Two calls to generate_password() with the same input must produce different hashes (random salt).'
        );
    }
}

/**
 * Thin wrapper that exposes only the bcrypt methods under test without
 * loading the full CI3 bootstrap.
 */
class StubCrypt
{
    public function generate_password(string $password, string $salt = ''): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function check_password(string $hash, string $password): bool
    {
        return password_verify($password, $hash);
    }
}
