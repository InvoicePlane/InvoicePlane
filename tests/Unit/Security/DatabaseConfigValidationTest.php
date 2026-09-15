<?php

namespace Tests\Unit\Security;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Database Configuration Validation Tests.
 *
 * Tests the validation of database configuration parameters,
 * particularly special character handling in passwords.
 *
 * write_database_config() stores DB_* values wrapped in single quotes
 * (DB_PASSWORD='...'), and phpdotenv performs no escape-sequence processing
 * inside single-quoted values - every character is taken literally. The
 * only character that cannot appear is a literal single quote, since it
 * would terminate the value early.
 */
#[Group('security')]
class DatabaseConfigValidationTest extends TestCase
{
    protected function setUp(): void
    {
        require_once dirname(__DIR__, 3) . '/application/helpers/file_security_helper.php';
    }

    /**
     * Provider for valid passwords with special characters.
     *
     * All of these are safe inside a single-quoted phpdotenv value,
     * including characters (\, ") that would require escaping - or would
     * break parsing entirely - inside a double-quoted value.
     */
    public static function validPasswordProvider(): array
    {
        return [
            'simple password'            => ['password123'],
            'password with hyphens'      => ['pass-word-123'],
            'password with underscore'   => ['pass_word_123'],
            'password with hash'         => ['#Ex4mpl3Pass'],
            'password with dollar sign'  => ['Pass$word123'],
            'password with exclamation'  => ['Pass!word123'],
            'password with at symbol'    => ['Pass@word123'],
            'password with special mix'  => ['#Ex4mpl3$Pass!'],
            'password with braces'       => ['Pass{word}123'],
            'password with brackets'     => ['Pass[word]123'],
            'password with pipe'         => ['Pass|word|123'],
            'password with ampersand'    => ['Pass&word&123'],
            'password with percent'      => ['Pass%word%123'],
            'password with caret'        => ['Pass^word^123'],
            'password with tilde'        => ['Pass~word~123'],
            'password with comma'        => ['Pass,word,123'],
            'password with period'       => ['Pass.word.123'],
            'password with colon'        => ['Pass:word:123'],
            'password with slash'        => ['Pass/word/123'],
            'password with backslash'    => ['Pass\\word\\123'],
            'password with double quote' => ['Pass"word"123'],
            'password with semicolon'    => ['Pass;word;123'],
            'password with parentheses'  => ['Pass(word)123'],
            'password with equals'       => ['Pass=word=123'],
            'password with plus'         => ['Pass+word+123'],
            'password with asterisk'     => ['Pass*word*123'],
            'password with question'     => ['Pass?word?123'],
            'password with less than'    => ['Pass<word<123'],
            'password with greater than' => ['Pass>word>123'],
            'the reported issue password' => ['ThisPa$$wo"rd;IsWh@ck'],
            'empty password'             => [''],
        ];
    }

    /**
     * Provider for invalid passwords with control characters.
     */
    public static function invalidPasswordProvider(): array
    {
        return [
            'newline in password'  => ["pass\nword", 'newline_detected'],
            'carriage return'      => ["pass\rword", 'newline_detected'],
            'null byte'            => ["pass\0word", 'null_byte'],
            'tab character'        => ["pass\tword", 'invalid_password_format'],
            'form feed'            => ["pass\fword", 'invalid_password_format'],
        ];
    }

    #[Test]
    #[DataProvider('validPasswordProvider')]
    public function it_accepts_passwords_with_special_characters(string $password): void
    {
        /* Arrange */

        /* Act */
        $result = validate_db_config_parameter($password, 'password');

        /* Assert */
        self::assertTrue($result['valid'], "Password should be valid: {$password}");
        self::assertSame($password, $result['sanitized']);
    }

    #[Test]
    #[DataProvider('invalidPasswordProvider')]
    public function it_rejects_passwords_with_control_characters(string $password, string $expectedError): void
    {
        /* Arrange */

        /* Act */
        $result = validate_db_config_parameter($password, 'password');

        /* Assert */
        self::assertFalse($result['valid']);
        self::assertSame($expectedError, $result['error']);
    }

    #[Test]
    public function it_rejects_passwords_containing_a_single_quote(): void
    {
        /* Arrange */
        $password = "pass'word";

        /* Act */
        $result = validate_db_config_parameter($password, 'password');

        /* Assert */
        self::assertFalse($result['valid'], 'A single quote cannot appear in a single-quoted DB_PASSWORD value');
        self::assertSame('password_contains_quote', $result['error']);
    }

    #[Test]
    public function it_rejects_a_password_that_is_only_a_single_quote(): void
    {
        /* Arrange */

        /* Act */
        $result = validate_db_config_parameter("'", 'password');

        /* Assert */
        self::assertFalse($result['valid']);
        self::assertSame('password_contains_quote', $result['error']);
    }

    #[Test]
    public function it_rejects_empty_hostname(): void
    {
        /* Arrange */

        /* Act */
        $result = validate_db_config_parameter('', 'hostname');

        /* Assert */
        self::assertFalse($result['valid']);
        self::assertSame('empty_value', $result['error']);
    }

    #[Test]
    public function it_rejects_empty_username(): void
    {
        /* Arrange */

        /* Act */
        $result = validate_db_config_parameter('', 'username');

        /* Assert */
        self::assertFalse($result['valid']);
        self::assertSame('empty_value', $result['error']);
    }

    #[Test]
    public function it_rejects_empty_database(): void
    {
        /* Arrange */

        /* Act */
        $result = validate_db_config_parameter('', 'database');

        /* Assert */
        self::assertFalse($result['valid']);
        self::assertSame('empty_value', $result['error']);
    }

    #[Test]
    public function it_allows_empty_password(): void
    {
        /* Arrange */

        /* Act */
        $result = validate_db_config_parameter('', 'password');

        /* Assert */
        self::assertTrue($result['valid']);
        self::assertSame('', $result['sanitized']);
    }

    #[Test]
    public function it_rejects_hostnames_containing_a_single_quote(): void
    {
        /* Arrange */

        /* Act */
        $result = validate_db_config_parameter("db'host", 'hostname');

        /* Assert */
        self::assertFalse($result['valid']);
        self::assertSame('invalid_hostname_format', $result['error']);
    }

    #[Test]
    public function it_rejects_usernames_containing_a_single_quote(): void
    {
        /* Arrange */

        /* Act */
        $result = validate_db_config_parameter("us'er", 'username');

        /* Assert */
        self::assertFalse($result['valid']);
        self::assertSame('invalid_username_format', $result['error']);
    }

    #[Test]
    public function it_rejects_database_names_containing_a_single_quote(): void
    {
        /* Arrange */

        /* Act */
        $result = validate_db_config_parameter("my'db", 'database');

        /* Assert */
        self::assertFalse($result['valid']);
        self::assertSame('invalid_database_format', $result['error']);
    }
}
