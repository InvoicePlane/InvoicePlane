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
        require_once dirname(__DIR__, 3) . '/application/helpers/ip_security_helper.php';
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
