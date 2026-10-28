<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * GHSA-qq8q-gf24-576m: archived PDFs must be stored under an unguessable name. pdf_create() ends
 * in mPDF's Output('I'), which emits headers and the PDF body directly, so the streaming
 * branches can only be observed from outside the process (tests/Support/pdf_stream_probe.php).
 */
final class PdfStreamingTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = sys_get_temp_dir() . '/pdf-stream-' . bin2hex(random_bytes(6));
        mkdir($this->root . '/archive', 0777, true);
        mkdir($this->root . '/temp', 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeTree($this->root);

        parent::tearDown();
    }

    #[Test]
    public function it_streams_a_new_invoice_pdf_and_archives_it_under_an_unguessable_name(): void
    {
        /* Arrange */
        $args = ['filename' => 'INV-1', 'stream' => true, 'isInvoice' => true];

        /* Act */
        $result = $this->probe($args);

        /* Assert: the browser gets a PDF, and the archived copy is "<token>_<filename>.pdf" with no date or counter in front */
        self::assertSame('%PDF-', $result['stream_head']);
        self::assertCount(1, $result['archive']);
        self::assertMatchesRegularExpression('/^[0-9a-f]{32}_INV-1\.pdf$/', $result['archive'][0]);
    }

    #[Test]
    public function it_returns_the_unguessable_archive_path_instead_of_streaming_when_not_streaming(): void
    {
        /* Arrange */
        $args = ['filename' => 'INV-1', 'stream' => false, 'isInvoice' => true];

        /* Act */
        $result = $this->probe($args);

        /* Assert */
        self::assertSame(0, $result['stream_len']);
        self::assertMatchesRegularExpression('#^archive/[0-9a-f]{32}_INV-1\.pdf$#', $result['returned']);
        self::assertSame([basename($result['returned'])], $result['archive']);
    }

    #[Test]
    public function it_streams_a_non_invoice_pdf_without_writing_it_to_disk(): void
    {
        /* Arrange */
        $args = ['filename' => 'QUO-1', 'stream' => true, 'isInvoice' => null];

        /* Act */
        $result = $this->probe($args);

        /* Assert */
        self::assertSame('%PDF-', $result['stream_head']);
        self::assertSame([], $result['archive']);
        self::assertSame(['mpdf'], $result['temp']);
    }

    #[Test]
    public function it_saves_a_non_streamed_non_invoice_pdf_to_the_temp_folder_and_returns_that_path(): void
    {
        /* Arrange */
        $args = ['filename' => 'QUO-1', 'stream' => false, 'isInvoice' => null];

        /* Act */
        $result = $this->probe($args);

        /* Assert */
        self::assertSame(0, $result['stream_len']);
        self::assertSame('temp/QUO-1.pdf', $result['returned']);
        self::assertContains('QUO-1.pdf', $result['temp']);
        self::assertSame([], $result['archive']);
    }

    #[Test]
    public function it_streams_a_guest_download_of_an_already_archived_invoice_without_archiving_it_again(): void
    {
        /* Arrange: an earlier archive copy of this invoice already exists */
        $args = ['filename' => 'INV-1', 'stream' => true, 'isInvoice' => true, 'isGuest' => true, 'seed' => ['2026-01-01_INV-1.pdf']];

        /* Act */
        $result = $this->probe($args);

        /* Assert */
        self::assertSame('%PDF-', $result['stream_head']);
        self::assertSame(['2026-01-01_INV-1.pdf'], $result['archive']);
    }

    #[Test]
    public function it_reuses_the_archived_copy_a_previous_generation_of_this_invoice_left_behind(): void
    {
        /* Arrange: the name a first, non-guest generation would have produced */
        $existing = str_repeat('ab', 16) . '_INV-1.pdf';
        $args     = ['filename' => 'INV-1', 'stream' => false, 'isInvoice' => true, 'isGuest' => true, 'seed' => [$existing]];

        /* Act */
        $result = $this->probe($args);

        /* Assert: the guest is handed that copy and nothing new is written */
        self::assertSame('archive/' . $existing, $result['returned']);
        self::assertSame([$existing], $result['archive']);
    }

    #[Test]
    public function it_hands_a_guest_the_newest_of_several_archived_copies(): void
    {
        /* Arrange: the lexically smallest name is the oldest, the largest is in the middle */
        $oldest = str_repeat('00', 16) . '_INV-1.pdf';
        $newest = str_repeat('55', 16) . '_INV-1.pdf';
        $middle = str_repeat('ff', 16) . '_INV-1.pdf';
        $args   = ['filename' => 'INV-1', 'stream' => false, 'isInvoice' => true, 'isGuest' => true, 'seed' => [
            ['name' => $oldest, 'mtime' => 1_700_000_000],
            ['name' => $newest, 'mtime' => 1_700_002_000],
            ['name' => $middle, 'mtime' => 1_700_001_000],
        ]];

        /* Act */
        $result = $this->probe($args);

        /* Assert */
        self::assertSame('archive/' . $newest, $result['returned']);
    }

    #[Test]
    public function it_does_not_hand_a_guest_the_archived_copy_of_a_different_invoice(): void
    {
        /* Arrange: INV-11 and XINV-1 both end in "1.pdf" and one even ends in "INV-1.pdf" */
        $other     = str_repeat('cd', 16) . '_INV-11.pdf';
        $lookalike = str_repeat('ef', 16) . 'XINV-1.pdf';
        $args      = ['filename' => 'INV-1', 'stream' => false, 'isInvoice' => true, 'isGuest' => true, 'seed' => [$other, $lookalike]];

        /* Act */
        $result = $this->probe($args);

        /* Assert: a fresh copy of INV-1 was archived instead, and the other documents were left alone */
        self::assertMatchesRegularExpression('#^archive/[0-9a-f]{32}_INV-1\.pdf$#', $result['returned']);
        self::assertCount(3, $result['archive']);
        self::assertContains($other, $result['archive']);
        self::assertContains($lookalike, $result['archive']);
    }

    #[Test]
    public function it_only_matches_an_archive_whose_name_ends_with_this_invoices_name(): void
    {
        /* Arrange: this invoice's name appears in the middle of another file's name */
        $other = str_repeat('34', 16) . '_INV-1.pdf_older.pdf';
        $args  = ['filename' => 'INV-1', 'stream' => false, 'isInvoice' => true, 'isGuest' => true, 'seed' => [$other]];

        /* Act */
        $result = $this->probe($args);

        /* Assert */
        self::assertNotSame('archive/' . $other, $result['returned']);
        self::assertMatchesRegularExpression('#^archive/[0-9a-f]{32}_INV-1\.pdf$#', $result['returned']);
    }

    #[Test]
    public function it_treats_glob_metacharacters_in_the_filename_literally_when_looking_for_an_archive(): void
    {
        /* Arrange: as a pattern "INV-*" would match the other invoice's archive */
        $other = str_repeat('12', 16) . '_INV-7.pdf';
        $args  = ['filename' => 'INV-*', 'stream' => false, 'isInvoice' => true, 'isGuest' => true, 'seed' => [$other]];

        /* Act */
        $result = $this->probe($args);

        /* Assert */
        self::assertNotSame('archive/' . $other, $result['returned']);
        self::assertMatchesRegularExpression('#^archive/[0-9a-f]{32}_INV-\*\.pdf$#', $result['returned']);
    }

    #[Test]
    public function it_archives_a_fresh_copy_on_each_non_guest_generation_even_when_one_exists(): void
    {
        /* Arrange */
        $existing = str_repeat('ab', 16) . '_INV-1.pdf';
        $args     = ['filename' => 'INV-1', 'stream' => false, 'isInvoice' => true, 'seed' => [$existing]];

        /* Act */
        $result = $this->probe($args);

        /* Assert: staff always get a freshly rendered document, and its archive copy is a second file */
        self::assertCount(2, $result['archive']);
        self::assertNotSame('archive/' . $existing, $result['returned']);
    }

    private function removeTree(string $path): void
    {
        foreach (scandir($path) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            is_dir($path . '/' . $entry) ? $this->removeTree($path . '/' . $entry) : unlink($path . '/' . $entry);
        }
        rmdir($path);
    }

    /**
     * @return array{returned: ?string, stream_head: string, stream_len: int, archive: list<string>, temp: list<string>}
     */
    private function probe(array $args): array
    {
        $payload = base64_encode((string) json_encode($args + ['root' => $this->root], JSON_THROW_ON_ERROR));
        $process = proc_open(
            [PHP_BINARY, dirname(__DIR__, 2) . '/Support/pdf_stream_probe.php', $payload],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
        );
        self::assertIsResource($process);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        proc_close($process);

        $decoded = json_decode((string) $stdout, true);
        self::assertIsArray($decoded, 'probe produced no JSON: ' . $stdout . $stderr);

        return $decoded;
    }
}
