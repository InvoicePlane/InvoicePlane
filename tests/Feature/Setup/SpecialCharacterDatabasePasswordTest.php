<?php

namespace Tests\Feature\Setup;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Special Character Database Password Feature Tests.
 *
 * Tests that the setup wizard correctly handles database passwords
 * containing special characters like #, $, ;, ", \, etc.
 *
 * DB_* values are stored single-quoted in ipconfig.php (DB_PASSWORD='...').
 * phpdotenv's single-quoted state does zero escape-sequence processing, so
 * every character here is safe except a literal single quote, which
 * validate_db_config_parameter() rejects up front with 'password_contains_quote'.
 */
#[Group('setup')]
class SpecialCharacterDatabasePasswordTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        require_once dirname(__DIR__, 3) . '/application/helpers/file_security_helper.php';
    }

    /**
     * Provider for passwords with special characters.
     */
    public static function specialPasswordProvider(): array
    {
        return [
            'hash symbol'          => ['#Ex4mpl3Pass'],
            'dollar sign'          => ['Pass$word123'],
            'semicolon'            => ['Pass;word123'],
            'hash and dollar'      => ['#Ex4mpl3$Pass!'],
            'multiple symbols'     => ['P@ssw0rd!#$%'],
            'backslash'            => ['Pass\\word\\123'],
            'double quote'         => ['Pass"word"123'],
            'mixed special'        => ['#P@ss$w0rd!'],
            'the reported issue'   => ['ThisPa$$wo"rd;IsWh@ck'],
        ];
    }

    /**
     * Test that special character password validation passes.
     */
    #[Test]
    #[DataProvider('specialPasswordProvider')]
    public function it_accepts_passwords_with_special_characters_in_validation(string $password): void
    {
        /* Arrange */
        $validationData = [
            'hostname' => 'localhost',
            'username' => 'root',
            'password' => $password,
            'database' => 'test_db',
            'port'     => '3306',
        ];

        /* Act */
        $hostnameValidation = validate_db_config_parameter($validationData['hostname'], 'hostname');
        $usernameValidation = validate_db_config_parameter($validationData['username'], 'username');
        $passwordValidation = validate_db_config_parameter($validationData['password'], 'password');
        $databaseValidation = validate_db_config_parameter($validationData['database'], 'database');
        $portValidation = validate_db_config_parameter($validationData['port'], 'port');

        /* Assert */
        self::assertTrue($hostnameValidation['valid'], 'Hostname validation should pass');
        self::assertTrue($usernameValidation['valid'], 'Username validation should pass');
        self::assertTrue($passwordValidation['valid'], "Password validation should pass for: {$password}");
        self::assertTrue($databaseValidation['valid'], 'Database validation should pass');
        self::assertTrue($portValidation['valid'], 'Port validation should pass');

        self::assertSame($password, $passwordValidation['sanitized'], 'Password should not be modified');
    }

    #[Test]
    public function it_rejects_a_password_containing_a_single_quote(): void
    {
        /* Arrange */
        $password = "pass'word";

        /* Act */
        $result = validate_db_config_parameter($password, 'password');

        /* Assert */
        self::assertFalse($result['valid'], 'A single quote breaks out of the single-quoted DB_PASSWORD value');
        self::assertSame('password_contains_quote', $result['error']);
    }

    #[Test]
    public function it_prevents_newline_injection_in_passwords(): void
    {
        /* Arrange */
        $maliciousPassword = "password\nDB_DATABASE=hacked";

        /* Act */
        $result = validate_db_config_parameter($maliciousPassword, 'password');

        /* Assert */
        self::assertFalse($result['valid']);
        self::assertSame('newline_detected', $result['error']);
    }

    #[Test]
    public function it_prevents_null_byte_injection_in_passwords(): void
    {
        /* Arrange */
        $maliciousPassword = "password\0hacked";

        /* Act */
        $result = validate_db_config_parameter($maliciousPassword, 'password');

        /* Assert */
        self::assertFalse($result['valid']);
        self::assertSame('null_byte', $result['error']);
    }

    /**
     * Simulates what write_database_config() does: wrap the value in single
     * quotes with no escaping, then read it back the way phpdotenv's
     * single-quoted state does (strip quotes, no escape processing).
     */
    #[Test]
    #[DataProvider('specialPasswordProvider')]
    public function it_round_trips_special_characters_through_the_config_file_unescaped(string $password): void
    {
        /* Arrange */

        /* Act */
        $configLine = "DB_PASSWORD='" . $password . "'";

        if (preg_match("/^DB_PASSWORD='(.*)'$/", $configLine, $matches)) {
            $readBack = $matches[1];
        }

        /* Assert */
        self::assertSame($password, $readBack ?? null, "Password should survive the single-quote round trip unescaped: {$password}");
    }
}
