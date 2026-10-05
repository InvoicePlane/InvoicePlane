<?php

namespace Tests\Unit\Core\Integrations;

use EInvoiceProfile;
use EInvoiceProfileRegistry;
use IncomingInvoiceDocumentService;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Documents downloaded from an e-invoicing provider are untrusted input. archive() must validate
 * them, store them under a content hash (never a caller-chosen path) and refuse everything else.
 */
#[CoversClass(IncomingInvoiceDocumentService::class)]
final class IncomingInvoiceDocumentServiceTest extends TestCase
{
    private const XML = '<?xml version="1.0"?><Facturae><Invoice>42</Invoice></Facturae>';

    private string $archive;

    protected function setUp(): void
    {
        parent::setUp();
        $this->archive = sys_get_temp_dir() . '/ip-archive-test-' . bin2hex(random_bytes(6));
        mkdir($this->archive, 0750, true);
    }

    protected function tearDown(): void
    {
        $this->removeTree($this->archive);
        parent::tearDown();
    }

    // -- accepted documents --------------------------------------------------

    #[Test]
    public function it_archives_a_valid_xml_invoice_under_its_content_hash(): void
    {
        /* Act */
        $result = $this->service()->archive('testprov', ['profile' => 'TestXml'], $this->download(self::XML, 'invoice.xml'), $this->archive);

        /* Assert */
        $hash = hash('sha256', self::XML);
        self::assertSame('incoming/testprov/' . $hash . '.xml', $result['document_path']);
        self::assertSame($hash, $result['document_sha256']);
        self::assertSame(strlen(self::XML), $result['document_size']);
        self::assertSame('application/xml', $result['document_mime_type']);
        self::assertSame('TestXml', $result['document_profile']);
        self::assertSame('valid', $result['document_validation_status']);
        self::assertNull($result['document_validation_error']);
        $stored = $this->archive . '/' . $result['document_path'];
        self::assertSame(self::XML, file_get_contents($stored));
        self::assertSame('0640', substr(sprintf('%o', fileperms($stored)), -4), 'Archived documents must not be world-readable.');
    }

    #[Test]
    public function it_stores_identical_content_once_and_leaves_no_temporary_files_behind(): void
    {
        /* Act */
        $first  = $this->service()->archive('testprov', ['profile' => 'TestXml'], $this->download(self::XML, 'a.xml'), $this->archive);
        $second = $this->service()->archive('testprov', ['profile' => 'TestXml'], $this->download(self::XML, 'b.xml'), $this->archive);

        /* Assert */
        self::assertSame($first['document_path'], $second['document_path']);
        self::assertSame('b.xml', $second['document_name'], 'Each delivery keeps its own display name.');
        self::assertCount(1, glob($this->archive . '/incoming/testprov/*'), 'Same bytes, one stored file, no .incoming-* leftovers.');
    }

    #[Test]
    public function it_keeps_each_providers_documents_in_its_own_directory(): void
    {
        /* Arrange */
        $registry = $this->registry(['testprov', 'otherprov']);

        /* Act */
        $a = (new IncomingInvoiceDocumentService($registry))->archive('testprov', ['profile' => 'TestXml'], $this->download(self::XML), $this->archive);
        $b = (new IncomingInvoiceDocumentService($registry))->archive('otherprov', ['profile' => 'TestXml'], $this->download(self::XML), $this->archive);

        /* Assert */
        self::assertNotSame($a['document_path'], $b['document_path']);
        self::assertFileExists($this->archive . '/' . $a['document_path']);
        self::assertFileExists($this->archive . '/' . $b['document_path']);
    }

    /** @return array<string, array{0: mixed, 1: string}> */
    public static function filenames(): array
    {
        return [
            'plain'                       => ['invoice.xml', 'invoice.xml'],
            'missing extension gets one'  => ['invoice', 'invoice.xml'],
            'wrong extension gets one'    => ['invoice.pdf', 'invoice.pdf.xml'],
            'directory traversal'         => ['../../etc/passwd', 'passwd.xml'],
            'windows traversal'           => ['..\\..\\boot.ini', 'boot.ini.xml'],
            'markup and control chars'    => ["in<script>v\x00oice.xml", 'in_script_v_oice.xml'],
            'unicode letters are kept'    => ['facture-é-ü.xml', 'facture-é-ü.xml'],
            'only junk'                   => ['  ...  ', 'incoming-invoice.xml'],
            'not a string'                => [null, 'incoming-invoice.xml'],
            'empty'                       => ['', 'incoming-invoice.xml'],
        ];
    }

    #[Test]
    #[DataProvider('filenames')]
    public function it_sanitizes_the_display_filename(mixed $given, string $expected): void
    {
        /* Act */
        $result = $this->service()->archive('testprov', ['profile' => 'TestXml'], $this->download(self::XML, $given), $this->archive);

        /* Assert */
        self::assertSame($expected, $result['document_name']);
    }

    #[Test]
    public function it_caps_the_display_filename_length(): void
    {
        /* Act */
        $result = $this->service()->archive('testprov', ['profile' => 'TestXml'], $this->download(self::XML, str_repeat('a', 400) . '.xml'), $this->archive);

        /* Assert */
        self::assertLessThanOrEqual(255, mb_strlen($result['document_name']));
    }

    // -- rejected downloads --------------------------------------------------

    #[Test]
    public function it_surfaces_the_providers_own_download_failure_message(): void
    {
        /* Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Gateway timeout');

        /* Act */
        $this->service()->archive('testprov', [], ['success' => false, 'message' => 'Gateway timeout'], $this->archive);
    }

    /** @return array<string, array{0: array<string,mixed>, 1: string}> */
    public static function badDownloads(): array
    {
        return [
            'generic failure'  => [['success' => false], 'Provider document download failed.'],
            'no content'       => [['success' => true], 'empty incoming invoice document'],
            'empty content'    => [['success' => true, 'content' => ''], 'empty incoming invoice document'],
            'array content'    => [['success' => true, 'content' => ['x']], 'empty incoming invoice document'],
            'over 15 MB'       => [['success' => true, 'content' => str_repeat('a', 15 * 1024 * 1024 + 1)], '15 MB limit'],
        ];
    }

    /** @param array<string,mixed> $download */
    #[Test]
    #[DataProvider('badDownloads')]
    public function it_rejects_unusable_downloads_without_touching_the_archive(array $download, string $messagePart): void
    {
        /* Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($messagePart);

        try {
            /* Act */
            $this->service()->archive('testprov', ['profile' => 'TestXml'], $download, $this->archive);
        } finally {
            self::assertSame([], glob($this->archive . '/*'), 'A rejected download must leave nothing in the archive.');
        }
    }

    /** @return array<string, array{0: string}> */
    public static function badProviderCodes(): array
    {
        return [
            'traversal'  => ['../escape'],
            'slash'      => ['a/b'],
            'uppercase'  => ['Provider'],
            'space'      => ['pro vider'],
            'leading digit' => ['1provider'],
            'empty'      => [''],
            'null byte'  => ["prov\0ider"],
        ];
    }

    #[Test]
    #[DataProvider('badProviderCodes')]
    public function it_rejects_a_provider_code_that_could_steer_the_archive_path(string $code): void
    {
        /* Assert */
        $this->expectException(InvalidArgumentException::class);

        try {
            /* Act */
            $this->service()->archive($code, ['profile' => 'TestXml'], $this->download(self::XML), $this->archive);
        } finally {
            self::assertSame([], glob($this->archive . '/*'));
            self::assertDirectoryDoesNotExist(dirname($this->archive) . '/escape');
        }
    }

    // -- XML profile detection and validation --------------------------------

    #[Test]
    public function it_rejects_content_that_is_neither_pdf_nor_well_formed_xml(): void
    {
        /* Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('neither a PDF nor well-formed XML');

        /* Act */
        $this->service()->archive('testprov', ['profile' => 'TestXml'], $this->download('<broken><xml>'), $this->archive);
    }

    #[Test]
    public function it_rejects_xml_whose_profile_cannot_be_identified(): void
    {
        /* Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('profile is unsupported or cannot be identified');

        /* Act: well-formed, but no declared profile and no CustomizationID */
        $this->service()->archive('testprov', [], $this->download(self::XML), $this->archive);
    }

    #[Test]
    public function it_will_not_trust_a_declared_profile_the_provider_is_not_enabled_for(): void
    {
        /* Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('unsupported or cannot be identified');

        /* Act: TestXml is only enabled for "testprov" */
        $this->service()->archive('intruder', ['profile' => 'TestXml'], $this->download(self::XML), $this->archive);
    }

    #[Test]
    public function it_rejects_xml_whose_root_does_not_match_the_declared_profile(): void
    {
        /* Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('XML root element does not match the selected profile.');

        /* Act */
        $this->service()->archive('testprov', ['profile' => 'TestXml'], $this->download('<?xml version="1.0"?><Invoice/>'), $this->archive);
    }

    // -- Factur-X (PDF) ------------------------------------------------------

    #[Test]
    public function it_refuses_a_pdf_from_a_provider_not_enabled_for_factur_x(): void
    {
        /* Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Provider is not enabled for incoming Factur-X documents.');

        /* Act */
        (new IncomingInvoiceDocumentService(EInvoiceProfileRegistry::builtIn()))->archive('letspeppol', [], $this->download('%PDF-1.7 body'), $this->archive);
    }

    #[Test]
    public function it_reports_a_missing_pdf_extractor_instead_of_crashing(): void
    {
        /* Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to inspect embedded Factur-X attachments.');

        /* Act */
        $this->facturx('/nonexistent/pdfdetach')->archive('qonto', [], $this->download('%PDF-1.7 body'), $this->archive);
    }

    #[Test]
    public function it_rejects_a_pdf_that_has_no_factur_x_attachment(): void
    {
        /* Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('does not contain a factur-x.xml attachment');

        /* Act */
        $this->facturx($this->fakePdfDetach(list: "1: logo.png\n2: terms.txt"))->archive('qonto', [], $this->download('%PDF-1.7 body'), $this->archive);
    }

    #[Test]
    public function it_rejects_a_pdf_when_listing_its_attachments_fails(): void
    {
        /* Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to inspect embedded Factur-X attachments.');

        /* Act */
        $this->facturx($this->fakePdfDetach(list: '', listExit: 3))->archive('qonto', [], $this->download('%PDF-1.7 body'), $this->archive);
    }

    #[Test]
    public function it_rejects_a_pdf_whose_attachment_cannot_be_extracted(): void
    {
        /* Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('does not expose a readable factur-x.xml attachment');

        /* Act */
        $this->facturx($this->fakePdfDetach(list: '1: factur-x.xml', saveExit: 1))->archive('qonto', [], $this->download('%PDF-1.7 body'), $this->archive);
    }

    #[Test]
    public function it_rejects_a_pdf_whose_extracted_attachment_is_empty(): void
    {
        /* Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('does not expose a readable factur-x.xml attachment');

        /* Act */
        $this->facturx($this->fakePdfDetach(list: '1: factur-x.xml', saveContent: ''))->archive('qonto', [], $this->download('%PDF-1.7 body'), $this->archive);
    }

    #[Test]
    public function it_validates_the_extracted_factur_x_xml_and_cleans_up_after_itself(): void
    {
        /* Arrange: extraction works, but the embedded XML is not a CII invoice */
        $before = count(glob(sys_get_temp_dir() . '/ip-{facturx,incoming}-*', GLOB_BRACE));

        try {
            /* Act */
            $this->facturx($this->fakePdfDetach(list: "3: factur-x.xml", saveContent: '<?xml version="1.0"?><NotCii/>'))
                ->archive('qonto', [], $this->download('%PDF-1.7 body'), $this->archive);
            self::fail('A PDF carrying a non-CII attachment must be rejected.');
        } catch (RuntimeException $e) {
            /* Assert: the validator's verdict is surfaced, and every staging file is gone */
            self::assertStringContainsString('XML root element does not match the selected profile.', $e->getMessage());
        }

        self::assertSame($before, count(glob(sys_get_temp_dir() . '/ip-{facturx,incoming}-*', GLOB_BRACE)));
        self::assertSame([], glob($this->archive . '/*'));
    }

    // -- helpers -------------------------------------------------------------

    private function service(): IncomingInvoiceDocumentService
    {
        return new IncomingInvoiceDocumentService($this->registry(['testprov']));
    }

    private function facturx(string $pdfDetach): IncomingInvoiceDocumentService
    {
        return new IncomingInvoiceDocumentService(EInvoiceProfileRegistry::builtIn(), null, $pdfDetach);
    }

    /** @param list<string> $providers */
    private function registry(array $providers): EInvoiceProfileRegistry
    {
        return new EInvoiceProfileRegistry([
            new EInvoiceProfile('TestXml', 'Test XML', 'XX', 'TestGen', 'test', 'facturae', 'application/xml', 'xml', false, '', [], null, null, null, $providers),
        ]);
    }

    /** @return array<string,mixed> */
    private function download(string $content, mixed $filename = 'document.xml'): array
    {
        return ['success' => true, 'content' => $content, 'filename' => $filename];
    }

    /** A stand-in for poppler's pdfdetach that prints/extracts what the scenario needs. */
    private function fakePdfDetach(string $list, int $listExit = 0, int $saveExit = 0, string $saveContent = '<x/>'): string
    {
        $path   = $this->archive . '-pdfdetach-' . bin2hex(random_bytes(3));
        $script = "#!/bin/sh\n"
            . "if [ \"\$1\" = \"-list\" ]; then printf '%s' " . escapeshellarg($list) . "; exit {$listExit}; fi\n"
            . "if [ \"\$1\" = \"-save\" ]; then printf '%s' " . escapeshellarg($saveContent) . " > \"\$4\"; exit {$saveExit}; fi\n"
            . "exit 9\n";
        file_put_contents($path, $script);
        chmod($path, 0700);
        register_shutdown_function(static fn () => is_file($path) && unlink($path));

        return $path;
    }

    private function removeTree(string $dir): void
    {
        if ( ! is_dir($dir)) {
            return;
        }
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $entry) {
            $entry->isDir() ? rmdir($entry->getPathname()) : unlink($entry->getPathname());
        }
        rmdir($dir);
    }
}
