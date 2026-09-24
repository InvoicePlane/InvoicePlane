<?php

namespace Tests\Unit\Security;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('security')]
class FileSecurityHelperTest extends TestCase
{
    protected function setUp(): void
    {
        require_once dirname(__DIR__, 3) . '/application/helpers/file_security_helper.php';
    }

    public static function unsafeFilenameProvider(): array
    {
        return [
            'empty'          => ['', 'empty_filename'],
            'unix escape'    => ['../../etc/passwd', 'path_traversal'],
            'windows escape' => ['..\\..\\boot.ini', 'path_traversal'],
            'null byte'      => ["invoice.pdf\0.php", 'null_byte'],
            'absolute'       => ['/etc/passwd', 'absolute_path'],
            'drive path'     => ['C:/Windows/win.ini', 'drive_letter'],
        ];
    }

    #[Test]
    #[DataProvider('unsafeFilenameProvider')]
    public function it_rejects_unsafe_filenames(string $filename, string $error): void
    {
        /* Arrange */

        /* Act */
        $result = validate_safe_filename($filename);

        /* Assert */
        self::assertFalse($result['valid']);
        self::assertSame($error, $result['error']);
    }

    #[Test]
    public function it_sanitizes_header_injection_and_uses_a_fallback_for_empty_output(): void
    {
        /* Arrange */
        $injected = "report\r\n\".pdf";
        $empty    = "\r\n\\\"";

        /* Act */
        $sanitized = sanitize_filename_for_header($injected);
        $fallback  = sanitize_filename_for_header($empty);

        /* Assert */
        self::assertSame('report.pdf', $sanitized);
        self::assertSame('attachment.bin', $fallback);
    }

    #[Test]
    public function it_sanitizes_document_numbers_to_filename_safe_characters(): void
    {
        /* Arrange */
        $documentNumbers = ['INV/2026:001', '../etc/passwd'];

        /* Act */
        $sanitized = array_map('sanitize_document_number_for_filename', $documentNumbers);

        /* Assert */
        self::assertSame(['INV_2026_001', '___etc_passwd'], $sanitized);
    }

    #[Test]
    public function it_validates_database_ports_in_the_valid_range(): void
    {
        /* Arrange */
        $ports = [null, '443', '0', '65536', '3306;DROP TABLE users'];

        /* Act */
        $validated = array_map('sanitize_database_port', $ports);

        /* Assert */
        self::assertSame([3306, 443, null, null, null], $validated);
    }

    #[Test]
    public function it_confines_resolved_files_to_the_allowed_directory(): void
    {
        /* Arrange */
        $directory = sys_get_temp_dir() . '/invoiceplane-file-security-' . bin2hex(random_bytes(4));
        mkdir($directory, 0700, true);
        $inside  = $directory . '/inside.txt';
        $outside = $directory . '-outside.txt';
        file_put_contents($inside, 'inside');
        file_put_contents($outside, 'outside');

        try {
            /* Act */
            $insideResult  = validate_file_in_directory($inside, $directory);
            $outsideResult = validate_file_in_directory($outside, $directory);

            /* Assert */
            self::assertTrue($insideResult);
            self::assertFalse($outsideResult);
        } finally {
            unlink($inside);
            unlink($outside);
            rmdir($directory);
        }
    }
}
