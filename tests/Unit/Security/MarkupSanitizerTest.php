<?php

namespace Tests\Unit\Security;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('security')]
class MarkupSanitizerTest extends TestCase
{
    protected function setUp(): void
    {
        require_once dirname(__DIR__, 3) . '/application/helpers/html_sanitizer_helper.php';
        require_once dirname(__DIR__, 3) . '/application/helpers/mpdf_helper.php';
    }

    #[Test]
    public function it_keeps_safe_email_markup_and_removes_script_content(): void
    {
        /* Arrange */
        $html = '<p>Hello <strong>world</strong></p><script>alert(1)</script>';

        /* Act */
        $sanitized = sanitize_email_template_html($html);

        /* Assert */
        self::assertStringContainsString('<p>Hello <strong>world</strong></p>', $sanitized);
        self::assertStringNotContainsString('<script', $sanitized);
        self::assertStringNotContainsString('alert(1)', $sanitized);
    }

    #[Test]
    public function it_removes_external_images_from_email_markup(): void
    {
        /* Arrange */
        $html = '<p><img src="https://attacker.example/track.gif"></p>';

        /* Act */
        $sanitized = sanitize_email_template_html($html);

        /* Assert */
        self::assertStringNotContainsString('attacker.example', $sanitized);
    }

    #[Test]
    public function it_keeps_only_safe_pdf_footer_tags_and_strips_attributes(): void
    {
        /* Arrange */
        $footer = '<strong class="unsafe">Footer</strong><script>alert(1)</script>';

        /* Act */
        $sanitized = sanitize_pdf_footer_content($footer);

        /* Assert */
        self::assertSame('<strong>Footer</strong>', $sanitized);
        self::assertStringNotContainsString('class=', $sanitized);
        self::assertStringNotContainsString('<script', $sanitized);
    }

    #[Test]
    public function it_converts_pdf_footer_break_tags_and_handles_null(): void
    {
        /* Arrange */
        $footer = 'Line one<br>Line two';

        /* Act */
        $sanitized = sanitize_pdf_footer_content($footer);
        $empty     = sanitize_pdf_footer_content(null);

        /* Assert */
        self::assertSame("Line one\nLine two", $sanitized);
        self::assertSame('', $empty);
    }
}
