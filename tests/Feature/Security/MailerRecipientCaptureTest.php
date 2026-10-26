<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;
use Tests\Concerns\RecordsSmtpEnvelope;

/**
 * GHSA-qw48-r3gw-gc45: Mailer allows arbitrary recipients and sender spoofing.
 *
 * The tests send the document through a real SMTP conversation with a local recording server and assert
 * on the envelope that actually went out: the recipient is the document's client and the sender is the
 * configured organisation address, whatever the form posted.
 */
#[Group('security')]
final class MailerRecipientCaptureTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;
    use RecordsSmtpEnvelope;

    protected function tearDown(): void
    {
        $this->stopSmtpRecorder();

        parent::tearDown();
    }

    #[Test]
    public function it_addresses_the_mail_to_the_invoice_client_and_ignores_a_posted_recipient(): void
    {
        /* Act */
        $envelope = $this->sendWith(['to_email' => 'attacker@evil.test']);

        /* Assert */
        self::assertContains('client@legit.test', $envelope['to'], 'The mail must reach the invoice client.');
        self::assertNotContains('attacker@evil.test', $envelope['to'], 'A POSTed to_email must never become a recipient.');
    }

    #[Test]
    public function it_sends_from_the_configured_organisation_address_and_ignores_a_posted_sender(): void
    {
        /* Act */
        $envelope = $this->sendWith(['from_email' => 'ceo@spoofed.test']);

        /* Assert */
        self::assertContains('billing@company.test', $envelope['from'], 'The sender must be the configured address.');
        self::assertNotContains('ceo@spoofed.test', $envelope['from'], 'A POSTed from_email must never become the sender.');
    }

    #[Test]
    public function it_binds_a_quote_mail_to_the_quote_client_and_the_configured_sender(): void
    {
        /* Act */
        $envelope = $this->sendWith(['to_email' => 'attacker@evil.test', 'from_email' => 'ceo@spoofed.test'], 'quote');

        /* Assert */
        self::assertContains('client@legit.test', $envelope['to']);
        self::assertNotContains('attacker@evil.test', $envelope['to']);
        self::assertContains('billing@company.test', $envelope['from']);
        self::assertNotContains('ceo@spoofed.test', $envelope['from']);
    }

    /**
     * @param array<string, string> $override
     *
     * @return array{to: list<string>, from: list<string>}
     */
    private function sendWith(array $override, string $kind = 'invoice'): array
    {
        $this->startSmtpRecorder();

        $clientId = $this->databaseInsert('ip_clients', [
            'client_date_created'  => date('Y-m-d H:i:s'),
            'client_date_modified' => date('Y-m-d H:i:s'),
            'client_name'          => 'Legit Client',
            'client_email'         => 'client@legit.test',
        ]);
        $documentId = $kind === 'quote' ? $this->seedQuote($clientId) : $this->seedInvoice($clientId);
        $this->actingAsAdmin();

        $this->postWithValidCsrfToken("/mailer/send_{$kind}/{$documentId}", array_merge([
            'to_email'     => 'client@legit.test',
            'from_email'   => 'billing@company.test',
            'from_name'    => 'Billing',
            'cc'           => '',
            'bcc'          => '',
            'subject'      => 'Mail',
            'body'         => 'Please pay.',
            'pdf_template' => '',
        ], $override));

        $envelope = $this->recordedEnvelope();
        self::assertNotEmpty($envelope['to'], 'The controller must have sent a mail through the recording SMTP server.');

        return $envelope;
    }

    private function seedQuote(int $clientId): int
    {
        return $this->databaseInsert('ip_quotes', [
            'user_id'             => 1,
            'client_id'           => $clientId,
            'invoice_id'          => 0,
            'invoice_group_id'    => 1,
            'quote_status_id'     => 1,
            'quote_date_created'  => date('Y-m-d'),
            'quote_date_modified' => date('Y-m-d H:i:s'),
            'quote_date_expires'  => date('Y-m-d', time() + 86400 * 30),
            'quote_number'        => 'Q-' . uniqid(),
            'quote_url_key'       => md5(uniqid('', true)),
        ]);
    }
}
