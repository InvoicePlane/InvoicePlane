<?php

namespace Tests\Unit\Setup;

use Dotenv\Dotenv;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Database Configuration File Writing/Reading Tests.
 *
 * Tests that ipconfig.php is written and parsed correctly for database
 * credentials containing special characters.
 *
 * write_database_config() wraps DB_* values in single quotes with no
 * escaping. phpdotenv's single-quoted state performs zero escape-sequence
 * processing - every character is taken literally until the closing quote -
 * so this is safe for any value that doesn't itself contain a single quote
 * (which validate_db_config_parameter() rejects before this is reached).
 *
 * Deliberately does not exercise this through Setup::write_database_config()
 * itself: that private method always targets the IPCONFIG_FILE constant,
 * which bootstrap/kernel.php points at the repo's real ipconfig.php - the
 * same file this entire test suite's own DB connection reads via env(). A
 * test that drove the real HTTP setup/configure_database endpoint (the only
 * public path to that method) would overwrite that shared file mid-run, and
 * would also need to pass SETUP_COMPLETED's guard and a live DB connectivity
 * check inside check_database() first. Instead, this replicates
 * write_database_config()'s exact quoting logic against an isolated temp
 * file (see writeConfig()) and reads the result back with the real
 * vlucas/phpdotenv Dotenv class (a runtime, not dev-only, dependency) rather
 * than a hand-rolled parser - so the assertions are backed by the actual
 * library's parsing behaviour, not a simulation of it.
 */
#[Group('setup')]
class DatabaseConfigFileTest extends TestCase
{
    private string $tempConfigFile;

    protected function setUp(): void
    {
        $this->tempConfigFile = sys_get_temp_dir() . '/ipconfig_test_' . bin2hex(random_bytes(4)) . '.php';

        $this->createMinimalConfigFile($this->tempConfigFile);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempConfigFile)) {
            unlink($this->tempConfigFile);
        }
    }

    private function createMinimalConfigFile(string $path): void
    {
        $content = <<<'PHP'
# <?php exit('No direct script access allowed'); ?>
# InvoicePlane Configuration File

IP_URL=http://localhost

DB_HOSTNAME=''
DB_USERNAME=''
DB_PASSWORD=''
DB_DATABASE=''
DB_PORT=
PHP;
        file_put_contents($path, $content);
    }

    /**
     * Simulates write_database_config()'s single-quote wrapping.
     */
    private function writeConfig(string $hostname, string $username, string $password, string $database, int $port): void
    {
        $config = file_get_contents($this->tempConfigFile);

        $config = preg_replace_callback(
            '/^DB_HOSTNAME=.*$/m',
            static fn () => "DB_HOSTNAME='" . $hostname . "'",
            $config
        );
        $config = preg_replace_callback(
            '/^DB_USERNAME=.*$/m',
            static fn () => "DB_USERNAME='" . $username . "'",
            $config
        );
        $config = preg_replace_callback(
            '/^DB_PASSWORD=.*$/m',
            static fn () => "DB_PASSWORD='" . $password . "'",
            $config
        );
        $config = preg_replace_callback(
            '/^DB_DATABASE=.*$/m',
            static fn () => "DB_DATABASE='" . $database . "'",
            $config
        );
        $config = preg_replace_callback(
            '/^DB_PORT=.*$/m',
            static fn () => 'DB_PORT=' . $port,
            $config
        );

        file_put_contents($this->tempConfigFile, $config);
    }

    /**
     * Reads DB_* values back using the real vlucas/phpdotenv parser. Uses
     * the array-backed loader rather than bootstrap/kernel.php's
     * createImmutable(): that variant writes into the process's global
     * $_ENV/getenv() state, which this test suite's own DB connection
     * already populated from the real ipconfig.php - mutating it here would
     * risk cross-contaminating that, not just this isolated temp file.
     * createArrayBacked() parses with the same underlying library and
     * grammar but returns a plain array with no global side effects.
     */
    private function readSingleQuotedValue(string $key): ?string
    {
        $values = Dotenv::createArrayBacked(
            dirname($this->tempConfigFile),
            basename($this->tempConfigFile)
        )->load();

        return $values[$key] ?? null;
    }

    /**
     * Provider for database credentials with special characters.
     *
     * Includes the exact password reported in the originating issue, plus
     * characters (backslash, double quote) that are dangerous or outright
     * fatal inside a double-quoted phpdotenv value but perfectly safe here.
     */
    public static function specialCharacterCredentialsProvider(): array
    {
        return [
            'password with hash'            => ['localhost', 'root', '#Ex4mpl3Pass', 'testdb', 3306],
            'password with dollar sign'     => ['localhost', 'root', 'Pass$word123', 'testdb', 3306],
            'password with semicolon'       => ['localhost', 'root', 'Pass;word123', 'testdb', 3306],
            'password with hash and dollar' => ['localhost', 'root', '#Ex4mpl3$Pass!', 'testdb', 3306],
            'password with double quotes'   => ['localhost', 'root', 'Pass"word"123', 'testdb', 3306],
            'password with backslash'       => ['localhost', 'root', 'Pass\\word\\123', 'testdb', 3306],
            'the reported issue password'   => ['localhost', 'root', 'ThisPa$$wo"rd;IsWh@ck', 'testdb', 3306],
            'complex password'              => ['localhost', 'root', 'P@ssw0rd!#$%&()', 'testdb', 3306],
            'username with at symbol'       => ['localhost', 'root@localhost', 'password', 'testdb', 3306],
            'database with underscore'      => ['localhost', 'root', 'password', 'test_db', 3306],
            'database with hyphen'          => ['localhost', 'root', 'password', 'test-db', 3306],
        ];
    }

    #[Test]
    #[DataProvider('specialCharacterCredentialsProvider')]
    public function it_writes_and_reads_config_with_special_characters(
        string $hostname,
        string $username,
        string $password,
        string $database,
        int $port
    ): void {
        /* Arrange */

        /* Act */
        $this->writeConfig($hostname, $username, $password, $database, $port);

        /* Assert */
        self::assertSame($hostname, $this->readSingleQuotedValue('DB_HOSTNAME'), 'Hostname should be read correctly');
        self::assertSame($username, $this->readSingleQuotedValue('DB_USERNAME'), 'Username should be read correctly');
        self::assertSame($password, $this->readSingleQuotedValue('DB_PASSWORD'), 'Password should be read correctly');
        self::assertSame($database, $this->readSingleQuotedValue('DB_DATABASE'), 'Database should be read correctly');
        // Dotenv returns every parsed value as a string, including the unquoted numeric DB_PORT.
        self::assertSame((string) $port, $this->readSingleQuotedValue('DB_PORT'), 'Port should be read correctly');
    }

    #[Test]
    public function it_applies_no_escaping_at_all_when_writing(): void
    {
        /* Arrange */
        $password = 'Pass\\word"test$123;end';

        /* Act */
        $this->writeConfig('localhost', 'root', $password, 'testdb', 3306);
        $writtenLine = null;
        foreach (file($this->tempConfigFile) as $line) {
            if (str_starts_with($line, 'DB_PASSWORD=')) {
                $writtenLine = rtrim($line, "\r\n");
            }
        }

        /* Assert */
        self::assertSame("DB_PASSWORD='" . $password . "'", $writtenLine, 'The value must be written byte-for-byte with no escaping');
    }

    #[Test]
    public function it_round_trips_a_password_containing_only_special_characters(): void
    {
        /* Arrange */
        $password = '#$;:@!%^&*(){}[]|<>,.?/~`+=';

        /* Act */
        $this->writeConfig('localhost', 'root', $password, 'testdb', 3306);

        /* Assert */
        self::assertSame($password, $this->readSingleQuotedValue('DB_PASSWORD'));
    }
}
