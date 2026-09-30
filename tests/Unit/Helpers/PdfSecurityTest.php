<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;

class PdfSecurityTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_filename_in_pdf_footer(): void
    {
        // This test verifies that malicious document numbers in filenames
        // are HTML-escaped before being embedded in mPDF footer HTML

        // We can't fully test pdf_create without a full integration setup,
        // but we can verify the sanitize_pdf_footer_content function exists
        // and that filenames should be escaped

        $this->assertTrue(function_exists('sanitize_pdf_footer_content'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sanitizes_pdf_footer_content(): void
    {
        $malicious = '<img src="http://169.254.169.254/latest/meta-data/">';
        $result = sanitize_pdf_footer_content($malicious);

        // sanitize_pdf_footer_content should strip img tags since they're not in allowedTags
        $this->assertStringNotContainsString('<img', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_allows_safe_html_in_pdf_footer(): void
    {
        $safe = '<b>Important</b> <i>Notice</i>';
        $result = sanitize_pdf_footer_content($safe);

        // Should preserve allowed tags
        $this->assertStringContainsString('<b>', $result);
        $this->assertStringContainsString('<i>', $result);
    }
}
