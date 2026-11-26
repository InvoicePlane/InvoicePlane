<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * GHSA-prmc-vrjx-5hxc: email_quote_status() sends an HTML email when a guest accepts or rejects a quote, and put the
 * client name straight into the subject and body, so a client name such as <script>...</script> became stored XSS in the
 * owner's mailbox. The name and link are now escaped. Nothing executed email_quote_status() in any test (the unit
 * bootstrap only stubs parse_template), so removing the escaping left every test green. This runs the real helper in a
 * clean PHP process with the CodeIgniter collaborators stubbed: tests/Fixtures/mailer_quote_status_probe.php.
 */
final class QuoteStatusEmailEscapingTest extends TestCase
{
    #[Test]
    public function it_escapes_markup_in_the_client_name_in_both_subject_and_body(): void
    {
        /* Act */
        $output = $this->probe('<script>alert(1)</script>"x');

        /* Assert */
        self::assertStringNotContainsString('<script>', $output, 'The client name must never reach the email as a tag.');
        self::assertStringContainsString('SUBJECT:Quote for &lt;script&gt;alert(1)&lt;/script&gt;&quot;x was accepted', $output);
        self::assertStringContainsString('BODY:Client &lt;script&gt;alert(1)&lt;/script&gt;&quot;x accepted quote Q-1', $output);
    }

    #[Test]
    public function it_still_builds_the_link_as_a_real_anchor(): void
    {
        /* Act */
        $output = $this->probe('Plain Client');

        /* Assert: positive control, the URL (which carries & and ") is encoded in the href and the link text
         * without breaking the anchor markup itself */
        $escaped = 'http://example.test//quotes/view/5?a=1&amp;b=&quot;x&quot;';
        self::assertStringContainsString('<a href="' . $escaped . '">' . $escaped . '</a>', $output);
        self::assertStringContainsString('SUBJECT:Quote for Plain Client was accepted (Q-1)', $output);
    }

    #[Test]
    public function it_sends_nothing_and_returns_false_when_mail_is_not_configured(): void
    {
        /* Act */
        $output = $this->probe('Plain Client', 'none');

        /* Assert */
        self::assertStringNotContainsString('SUBJECT:', $output, 'No email may be built when mail is not configured.');
        self::assertStringContainsString('RETURN:false', $output);
    }

    #[Test]
    public function it_reports_success_when_the_email_was_sent(): void
    {
        /* Act + Assert */
        self::assertStringContainsString('RETURN:true', $this->probe('Plain Client'));
    }

    private function probe(string $clientName, string $mailMode = 'phpmail'): string
    {
        $cmd    = escapeshellarg(PHP_BINARY) . ' -d display_errors=0 ' . escapeshellarg(dirname(__DIR__, 2) . '/Fixtures/mailer_quote_status_probe.php') . ' ' . escapeshellarg($clientName) . ' ' . escapeshellarg($mailMode) . ' 2>&1';
        $output = shell_exec($cmd);
        self::assertNotNull($output, 'The probe produced no output.');

        return (string) $output;
    }
}
