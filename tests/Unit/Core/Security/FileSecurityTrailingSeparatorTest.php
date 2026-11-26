<?php

namespace Tests\Unit\Core\Security;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Upstream #1688 swaps rtrim() for mb_rtrim() where the file security helper joins a base directory and a
 * file name (validate_file_in_directory(), validate_file_access(), validate_db_filename()). The swap must not
 * change what those functions accept or return: a base directory given with trailing separators, with a
 * multibyte name, or as the filesystem root, and the sibling-directory prefix that the appended separator
 * exists to refuse.
 *
 * mb_rtrim() only exists from PHP 8.4. CI also runs 8.2 and 8.3, where every test here dies with
 * "Call to undefined function mb_rtrim()" instead of passing.
 */
#[Group('security')]
final class FileSecurityTrailingSeparatorTest extends TestCase
{
    /** @var string[] */
    private array $directories = [];

    protected function setUp(): void
    {
        parent::setUp();
        require_once ROOT_PATH . '/application/helpers/file_security_helper.php';
    }

    protected function tearDown(): void
    {
        foreach ($this->directories as $directory) {
            foreach (glob($directory . '/*') ?: [] as $file) {
                unlink($file);
            }
            rmdir($directory);
        }
        parent::tearDown();
    }

    #[Test]
    public function it_resolves_a_file_in_a_base_directory_given_with_trailing_separators(): void
    {
        /* Arrange */
        $directory = $this->makeDirectory('plain-' . bin2hex(random_bytes(3)));
        file_put_contents($directory . '/invoice.pdf', 'pdf');

        /* Act */
        $result = validate_file_access('invoice.pdf', $directory . '///');

        /* Assert: one separator between directory and name, whatever came after the directory */
        self::assertTrue($result['valid'], (string) ($result['error'] ?? ''));
        self::assertSame(realpath($directory . '/invoice.pdf'), $result['path']);
        self::assertSame('invoice.pdf', $result['basename']);
    }

    #[Test]
    public function it_resolves_a_file_in_a_base_directory_with_a_multibyte_name(): void
    {
        /* Arrange */
        $directory = $this->makeDirectory('ünï-фактура-' . bin2hex(random_bytes(3)));
        file_put_contents($directory . '/invoice.pdf', 'pdf');

        /* Act */
        $access = validate_file_access('invoice.pdf', $directory . '/');
        $db     = validate_db_filename('invoice.pdf', $directory . '/');

        /* Assert */
        self::assertTrue($access['valid'], (string) ($access['error'] ?? ''));
        self::assertSame(realpath($directory . '/invoice.pdf'), $access['path']);
        self::assertNotNull($db);
        self::assertSame($directory . '/invoice.pdf', $db['path']);
    }

    #[Test]
    public function it_does_not_double_the_separator_when_the_database_filename_is_joined_to_the_base_directory(): void
    {
        /* Arrange */
        $directory = $this->makeDirectory('db-' . bin2hex(random_bytes(3)));
        file_put_contents($directory . '/stored.pdf', 'pdf');

        /* Act */
        $result = validate_db_filename('stored.pdf', $directory . '//');

        /* Assert */
        self::assertNotNull($result);
        self::assertSame($directory . '/stored.pdf', $result['path']);
        self::assertSame('stored.pdf', $result['basename']);
    }

    #[Test]
    public function it_refuses_a_sibling_directory_that_only_shares_the_name_prefix(): void
    {
        /* Arrange: /x/archive must not contain /x/archive-evil/secret.pdf */
        $directory = $this->makeDirectory('archive-' . bin2hex(random_bytes(3)));
        $sibling   = $this->makeDirectory(basename($directory) . '-evil');
        file_put_contents($sibling . '/secret.pdf', 'secret');
        file_put_contents($directory . '/ok.pdf', 'ok');

        /* Act */
        $outside = validate_file_in_directory($sibling . '/secret.pdf', $directory);
        $inside  = validate_file_in_directory($directory . '/ok.pdf', $directory . '/');

        /* Assert */
        self::assertFalse($outside, 'A directory sharing only the name prefix is not inside the base directory.');
        self::assertTrue($inside);
    }

    #[Test]
    public function it_treats_the_filesystem_root_as_a_base_directory_that_contains_its_files(): void
    {
        /* Arrange: realpath('/') is a bare separator, which trimming reduces to an empty string */
        $file = ROOT_PATH . '/composer.json';

        /* Act */
        $inside = validate_file_in_directory($file, '/');

        /* Assert */
        self::assertTrue($inside);
    }

    #[Test]
    public function it_still_refuses_a_traversing_file_name_before_any_path_is_built(): void
    {
        /* Arrange */
        $directory = $this->makeDirectory('trav-' . bin2hex(random_bytes(3)));

        /* Act */
        $result = validate_file_access('../../etc/passwd', $directory . '/');

        /* Assert */
        self::assertFalse($result['valid']);
    }

    private function makeDirectory(string $name): string
    {
        $directory = sys_get_temp_dir() . '/' . $name;
        mkdir($directory, 0700, true);
        $this->directories[] = $directory;

        return $directory;
    }
}
