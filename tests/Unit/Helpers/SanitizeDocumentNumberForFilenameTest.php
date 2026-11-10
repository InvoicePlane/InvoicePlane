<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * GHSA-54xh-8h4r-4f3r: sanitize_document_number_for_filename() turns a user-controlled invoice/quote number into a
 * string that is safe to use as one path segment.
 */
final class SanitizeDocumentNumberForFilenameTest extends TestCase
{
    protected function setUp(): void
    {
        require_once APPPATH . 'helpers/file_security_helper.php';
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function numbers(): array
    {
        return [
            'plain'                    => ['INV-2026_0007', 'INV-2026_0007'],
            'digits only'              => ['0001', '0001'],
            'slash'                    => ['2026/001', '2026_001'],
            'dot'                      => ['INV.7', 'INV_7'],
            'traversal'                => ['../../etc/passwd', '______etc_passwd'],
            'backslash traversal'      => ['..\\..\\boot.ini', '______boot_ini'],
            'absolute path'            => ['/etc/passwd', '_etc_passwd'],
            'space and quotes'         => ['a b"c\'d', 'a_b_c_d'],
            'shell metacharacters'     => ['a;b|c&d$(x)`y`', 'a_b_c_d__x__y_'],
            'null byte'                => ["INV\x00.php", 'INV__php'],
            'control characters'       => ["A\x01B\nC\rD\tE", 'A_B_C_D_E'],
            'only illegal characters'  => ['../', '___'],
            'empty string'             => ['', ''],
            'unicode letter (2 bytes)' => ['Ä', '__'],
            'unicode symbol (3 bytes)' => ['€', '___'],
            'mixed unicode'            => ['Rg-Ü1', 'Rg-__1'],
            'dash and underscore kept' => ['-_-', '-_-'],
        ];
    }

    #[Test]
    #[DataProvider('numbers')]
    public function it_replaces_every_byte_outside_the_filename_allowlist_with_an_underscore(string $input, string $expected): void
    {
        /* Act */
        $result = sanitize_document_number_for_filename($input);

        /* Assert */
        self::assertSame($expected, $result);
    }

    #[Test]
    #[DataProvider('numbers')]
    public function it_never_returns_a_path_separator_dot_or_control_character(string $input): void
    {
        /* Act */
        $result = sanitize_document_number_for_filename($input);

        /* Assert */
        self::assertSame(1, preg_match('/^[A-Za-z0-9_-]*$/', $result));
        self::assertSame(strlen($input), strlen($result), 'Replacement is one underscore per byte, so length is preserved.');
    }

    #[Test]
    public function it_produces_a_path_that_stays_inside_the_target_directory(): void
    {
        /* Arrange */
        $dir = rtrim(sys_get_temp_dir(), '/') . '/sanitize-docnum-' . bin2hex(random_bytes(6));
        mkdir($dir, 0700, true);

        try {
            /* Act */
            $path = $dir . '/invoice_' . sanitize_document_number_for_filename('../../../outside') . '.xml';
            file_put_contents($path, 'x');

            /* Assert */
            self::assertSame(realpath($dir), realpath(dirname($path)));
            self::assertFileExists($path);
            self::assertFileDoesNotExist(dirname($dir, 2) . '/outside.xml');
        } finally {
            foreach (glob($dir . '/*') ?: [] as $f) {
                @unlink($f);
            }
            @rmdir($dir);
        }
    }
}
