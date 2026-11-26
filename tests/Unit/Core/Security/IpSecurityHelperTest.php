<?php

namespace Tests\Unit\Security;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('security')]
class IpSecurityHelperTest extends TestCase
{
    protected function setUp(): void
    {
        require_once ROOT_PATH . '/application/helpers/ip_security_helper.php';
    }

    #[Test]
    public function it_generates_a_hexadecimal_token_with_the_requested_entropy(): void
    {
        /* Arrange */
        $length = 32;

        /* Act */
        $token = generate_secure_token($length);

        /* Assert */
        self::assertSame(64, strlen($token));
        self::assertMatchesRegularExpression('/\A[0-9a-f]{64}\z/', $token);
    }

    #[Test]
    public function it_rejects_a_non_positive_token_length(): void
    {
        /* Arrange */
        $length = 0;

        /* Act */
        try {
            generate_secure_token($length);
            $exception = null;
        } catch (InvalidArgumentException $error) {
            $exception = $error;
        }

        /* Assert */
        self::assertInstanceOf(InvalidArgumentException::class, $exception);
    }

    #[Test]
    public function it_generates_a_password_reset_token_with_256_bits_of_entropy(): void
    {
        /* Arrange */

        /* Act */
        $token = generate_password_reset_token();

        /* Assert */
        self::assertSame(64, strlen($token));
        self::assertMatchesRegularExpression('/\A[0-9a-f]{64}\z/', $token);
    }

    #[Test]
    public function it_hashes_a_password_reset_token_with_sha256(): void
    {
        /*
         * ip_users.user_passwordreset_token must hold a digest, never the raw token: the
         * emailed reset link carries the plaintext, so a DB backup, or SQL injection
         * elsewhere, must not yield a usable reset token. Every call site (generation,
         * both lookup paths in Sessions::passwordreset()) routes through this one helper,
         * so pinning it here is what catches a regression at any of them.
         */

        /* Arrange */
        $token = 'b2c3d4e5f60718293a4b5c6d7e8f901a2b3c4d5e6f708192a3b4c5d6e7f809a1';

        /* Act */
        $digest = hash_password_reset_token($token);

        /* Assert: a real SHA-256 digest of the token, not the token itself. */
        self::assertSame(hash('sha256', $token), $digest);
        self::assertNotSame($token, $digest);
        self::assertMatchesRegularExpression('/\A[0-9a-f]{64}\z/', $digest);
    }

    #[Test]
    public function it_generates_a_bcrypt_compatible_salt(): void
    {
        /* Arrange */

        /* Act */
        $salt = generate_secure_salt();

        /* Assert */
        self::assertSame(22, strlen($salt));
        self::assertMatchesRegularExpression('/\A[.\/[0-9A-Za-z]{22}\z/', $salt);
    }
}
