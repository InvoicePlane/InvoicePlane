<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;

class PdfSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Run standalone (not after some other file that happens to load this helper first),
        // this file's functions are otherwise undefined.
        require_once dirname(__DIR__, 3) . '/application/helpers/mpdf_helper.php';
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_filename_in_pdf_footer(): void
    {
        /* Arrange: regression for GHSA-ph5g-ffvw-r5vq — the invoice/quote number (free text)
         * is rendered into the mPDF footer unescaped, and mPDF fetches any remote resource
         * referenced in footer HTML, so an attacker-controlled number containing
         * <img src="http://..."> could trigger an outbound request (SSRF) purely by being
         * printed in the footer. */
        $malicious = 'INV-001<img src="http://169.254.169.254/latest/meta-data/">';

        /* Act */
        $result = pdf_footer_filename($malicious);

        /* Assert */
        $this->assertStringNotContainsString('<img', $result);
        $this->assertStringContainsString('&lt;img', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_replaces_underscores_with_spaces_in_the_pdf_footer_filename(): void
    {
        /* Arrange */
        $filename = 'INV_2026_00042';

        /* Act */
        $result = pdf_footer_filename($filename);

        /* Assert */
        $this->assertSame('INV 2026 00042', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sanitizes_pdf_footer_content(): void
    {
        /* Arrange: sanitize_pdf_footer_content() should strip img tags since they're not in
         * its allowedTags list. */
        $malicious = '<img src="http://169.254.169.254/latest/meta-data/">';

        /* Act */
        $result = sanitize_pdf_footer_content($malicious);

        /* Assert */
        $this->assertStringNotContainsString('<img', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_allows_safe_html_in_pdf_footer(): void
    {
        /* Arrange */
        $safe = '<b>Important</b> <i>Notice</i>';

        /* Act */
        $result = sanitize_pdf_footer_content($safe);

        /* Assert: allowed tags are preserved */
        $this->assertStringContainsString('<b>', $result);
        $this->assertStringContainsString('<i>', $result);
    }
}
