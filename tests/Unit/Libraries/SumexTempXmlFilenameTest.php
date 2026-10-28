<?php

namespace Tests\Unit\Libraries;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * GHSA-54xh-8h4r-4f3r: Sumex::pdf() writes the embedded SUMEX XML to STORAGE_TEMP_FOLDER . <name> . '.xml'. The
 * invoice number is user-controlled, so it must pass through sanitize_document_number_for_filename() before it becomes
 * part of that path. The helper itself is covered in SanitizeDocumentNumberForFilenameTest; this test pins the
 * call-site wiring by running the real Sumex::pdf() in a clean PHP process (tests/Fixtures/sumex/pdf_filename_probe.php,
 * all collaborators stubbed) and inspecting where the file actually landed.
 */
final class SumexTempXmlFilenameTest extends TestCase
{
    private string $dir = '';

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/sumex-filename-' . bin2hex(random_bytes(6));
        mkdir($this->dir, 0700, true);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->dir . '/*') ?: [] as $file) {
            @unlink($file);
        }
        @rmdir($this->dir);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function invoiceNumbers(): array
    {
        return [
            'path traversal'      => ['../../etc/passwd', 'invoice_______etc_passwd'],
            'absolute path'       => ['/var/www/html/shell', 'invoice__var_www_html_shell'],
            'backslash traversal' => ['..\\..\\win', 'invoice_______win'],
            'php extension'       => ['x.php', 'invoice_x_php'],
            'quotes and spaces'   => ['a b"c\'d', 'invoice_a_b_c_d'],
            'null byte'           => ["INV\x00.php", 'invoice_INV__php'],
            'newline'             => ["INV\nX", 'invoice_INV_X'],
            'unicode'             => ['Ä', 'invoice___'],
        ];
    }

    #[Test]
    #[DataProvider('invoiceNumbers')]
    public function it_confines_the_temp_xml_to_the_temp_folder_and_sanitises_the_invoice_number_in_its_name(string $invoiceNumber, string $expectedName): void
    {
        /* Act */
        $result = $this->probe($invoiceNumber);

        /* Assert */
        self::assertSame($expectedName, $result['filename']);
        self::assertMatchesRegularExpression($this->pathPattern($expectedName), $result['xml_path']);
        self::assertTrue($result['xml_exist'], 'The XML must be written to the sanitised path.');
        $written = array_map('basename', glob($this->dir . '/*') ?: []);
        self::assertCount(1, $written, 'Exactly one file, directly inside the temp folder.');
        self::assertMatchesRegularExpression('/^' . preg_quote($expectedName, '/') . '_[0-9a-f]{16}\.xml$/', $written[0]);
    }

    #[Test]
    public function it_keeps_a_normal_invoice_number_readable_in_the_temp_xml_name(): void
    {
        /* Act */
        $result = $this->probe('INV-2026_0007');

        /* Assert: positive control, the sanitiser does not mangle safe numbers */
        self::assertSame('invoice_INV-2026_0007', $result['filename']);
        self::assertMatchesRegularExpression($this->pathPattern('invoice_INV-2026_0007'), $result['xml_path']);
        self::assertTrue($result['xml_exist']);
    }

    #[Test]
    public function it_does_not_leak_the_raw_invoice_number_into_the_xml_file_name_or_pdf_filename(): void
    {
        /* Act */
        $result = $this->probe('../secret');

        /* Assert */
        self::assertStringNotContainsString('..', $result['filename']);
        self::assertStringNotContainsString('/', $result['filename']);
        self::assertStringNotContainsString('..', basename($result['xml_path']));
        self::assertSame(realpath($this->dir), realpath(dirname($result['xml_path'])));
    }

    /** The sanitised name, then the unguessable 16-hex suffix added for GHSA-65qw-2ppj-gjvm / GHSA-583r-4pw9-6rc9. */
    private function pathPattern(string $sanitisedName): string
    {
        return '#^' . preg_quote($this->dir . '/' . $sanitisedName, '#') . '_[0-9a-f]{16}\.xml$#';
    }

    /**
     * @return array{filename: string, xml_path: string, xml_exist: bool}
     */
    private function probe(string $invoiceNumber): array
    {
        $cmd = escapeshellarg(PHP_BINARY) . ' -d display_errors=0 '
            . escapeshellarg(dirname(__DIR__, 2) . '/Fixtures/sumex/pdf_filename_probe.php') . ' '
            . escapeshellarg(base64_encode($invoiceNumber)) . ' ' . escapeshellarg($this->dir) . ' 2>&1';

        $output = (string) shell_exec($cmd);
        self::assertSame(1, preg_match('/^PROBE:(.+)$/m', $output, $m), 'Probe did not report a result. Output: ' . $output);

        /** @var array{filename: string, xml_path: string, xml_exist: bool} $decoded */
        $decoded = json_decode($m[1], true, 512, JSON_THROW_ON_ERROR);

        return $decoded;
    }
}
