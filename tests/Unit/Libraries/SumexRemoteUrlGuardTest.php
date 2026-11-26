<?php

namespace Tests\Unit\Libraries;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * GHSA-pqjr-7x5w-gj3r / GHSA-qmh5-hgvc-6p6c: Sumex::pdf() posts the invoice XML to SUMEX_URL. It must refuse any
 * scheme other than https (file://, gopher://, plain http to internal hosts or cloud metadata), or a misconfigured
 * SUMEX_URL becomes an SSRF primitive. The previous test only searched Sumex.php's source text for the guard, so the
 * guard could be made dead code and it would still pass. SUMEX_URL is a constant (already defined by the test
 * bootstrap), so each case runs the real class in a clean PHP process: tests/Fixtures/sumex/pdf_probe.php.
 */
final class SumexRemoteUrlGuardTest extends TestCase
{
    /**
     * @return array<string, array{0: string}>
     */
    public static function refusedUrls(): array
    {
        return [
            'plain http'            => ['http://127.0.0.1:9/x'],
            'file'                  => ['file:///etc/passwd'],
            'gopher'                => ['gopher://127.0.0.1:9/_x'],
            'uppercase http scheme' => ['HTTP://127.0.0.1:9/x'],
            'no scheme'             => ['127.0.0.1:9/x'],
        ];
    }

    #[Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('refusedUrls')]
    public function it_refuses_to_send_the_request_to_a_non_https_url(string $url): void
    {
        /* Act */
        $output = $this->probe($url);

        /* Assert */
        self::assertStringContainsString('SUMEX_URL must use the https:// scheme', $output, 'The scheme guard must log the refusal.');
        self::assertStringContainsString('RESULT:false', $output, 'The request must not be sent; pdf() returns false.');
    }

    #[Test]
    public function it_does_not_trip_the_scheme_guard_for_an_https_url(): void
    {
        /* Act: connection to a closed local port is refused instantly, so nothing leaves the machine */
        $output = $this->probe('https://127.0.0.1:9/x');

        /* Assert: positive control, proving the guard rejects on the scheme and not on every URL */
        self::assertStringContainsString('RESULT:', $output, 'The probe must have run pdf() to completion.');
        self::assertStringNotContainsString('SUMEX_URL must use the https:// scheme', $output);
    }

    private function probe(string $url): string
    {
        $cmd    = escapeshellarg(PHP_BINARY) . ' -d display_errors=0 ' . escapeshellarg(dirname(__DIR__, 2) . '/Fixtures/sumex/pdf_probe.php') . ' ' . escapeshellarg($url) . ' 2>&1';
        $output = shell_exec($cmd);
        self::assertNotNull($output, 'The probe produced no output.');

        return (string) $output;
    }
}
