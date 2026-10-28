<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\RecordsSmtpEnvelope;

/**
 * GHSA-qw48-r3gw-gc45, cron path: the recurring-invoice cron e-mails the new invoice with the CC/BCC of the configured
 * e-mail template. A template copy address outside the organisation domain is refused like in the interactive path, but
 * the helper must not redirect (CodeIgniter's redirect() exits, which would abort the whole cron run), and the domain
 * that authorises copies must come from smtp_mail_from, not from the template's own From address, which the template
 * may set to any domain.
 */
#[Group('security')]
final class MailerCcBccCronTest extends AbstractTestCase
{
    use RecordsSmtpEnvelope;

    protected function tearDown(): void
    {
        $this->stopSmtpRecorder();

        parent::tearDown();
    }

    #[Test]
    public function it_refuses_an_outside_template_copy_without_aborting_the_cron_run(): void
    {
        /* Arrange: two recurring invoices are due; the template copies an outside address */
        $this->prepareCron('outsider@external.test', 'billing@company.test');
        $first  = $this->seedRecurringInvoice();
        $second = $this->seedRecurringInvoice();

        /* Act */
        $response = $this->get('/invoices/cron/recur/the-real-key');

        /* Assert: the run finished, both new invoices exist, nothing was sent */
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertDatabaseCount('ip_invoices', 2, ['client_id' => $first]);
        $this->assertDatabaseCount('ip_invoices', 2, ['client_id' => $second]);
        self::assertSame([], $this->recordedEnvelope()['to']);
    }

    #[Test]
    public function it_does_not_let_the_template_sender_domain_authorise_a_copy(): void
    {
        /* Arrange: template From and CC are both on external.test, smtp_mail_from is company.test */
        $this->prepareCron('partner@external.test', 'tpl@external.test');
        $this->seedRecurringInvoice();

        /* Act */
        $this->get('/invoices/cron/recur/the-real-key');

        /* Assert */
        self::assertNotContains('partner@external.test', $this->recordedEnvelope()['to']);
    }

    #[Test]
    public function it_delivers_a_template_copy_inside_the_organisation_domain(): void
    {
        /* Arrange: positive control */
        $this->prepareCron('boss@company.test', 'tpl@external.test');
        $this->seedRecurringInvoice();

        /* Act */
        $this->get('/invoices/cron/recur/the-real-key');

        /* Assert */
        $to = array_map('strtolower', $this->recordedEnvelope()['to']);
        self::assertContains('client@legit.test', $to);
        self::assertContains('boss@company.test', $to);
    }

    #[Test]
    public function it_does_not_abort_the_cron_run_for_a_recurring_invoice_with_an_invalid_client_address(): void
    {
        /* Arrange: one client address is not an e-mail address; the other invoice must still be processed */
        $this->prepareCron('boss@company.test', 'tpl@external.test');
        $bad  = $this->seedRecurringInvoice('not-an-email');
        $good = $this->seedRecurringInvoice();

        /* Act */
        $response = $this->get('/invoices/cron/recur/the-real-key');

        /* Assert: redirect() would have exited the run after the first failure */
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertDatabaseCount('ip_invoices', 2, ['client_id' => $bad]);
        $this->assertDatabaseCount('ip_invoices', 2, ['client_id' => $good]);
        self::assertContains('client@legit.test', array_map('strtolower', $this->recordedEnvelope()['to']));
    }

    private function prepareCron(string $templateCc, string $templateFrom): void
    {
        $this->startSmtpRecorder();
        $templateId = $this->databaseInsert('ip_email_templates', [
            'email_template_title'      => 'Cron template',
            'email_template_type'       => 'invoice',
            'email_template_body'       => 'Please pay.',
            'email_template_subject'    => 'Invoice',
            'email_template_from_name'  => 'Billing',
            'email_template_from_email' => $templateFrom,
            'email_template_cc'         => $templateCc,
            'email_template_bcc'        => '',
        ]);
        foreach ([
            'cron_key'                 => 'the-real-key',
            'invoices_due_after'       => '30',
            'automatic_email_on_recur' => '1',
            'email_invoice_template'   => (string) $templateId,
        ] as $key => $value) {
            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => $key, 'setting_value' => '']);
            $this->databaseUpdate('ip_settings', ['setting_value' => $value], ['setting_key' => $key]);
        }
    }

    private function seedRecurringInvoice(string $clientEmail = 'client@legit.test'): int
    {
        $clientId = $this->databaseInsert('ip_clients', [
            'client_date_created'  => date('Y-m-d H:i:s'),
            'client_date_modified' => date('Y-m-d H:i:s'),
            'client_name'          => 'Legit Client ' . bin2hex(random_bytes(3)),
            'client_email'         => $clientEmail,
        ]);
        $invoiceId = $this->seedInvoice($clientId, ['invoice_number' => 'CRON-SRC-' . bin2hex(random_bytes(4))]);
        $this->databaseInsert('ip_invoices_recurring', [
            'invoice_id'       => $invoiceId,
            'recur_start_date' => date('Y-m-d', strtotime('-1 day')),
            'recur_end_date'   => null,
            'recur_frequency'  => '1M',
            'recur_next_date'  => date('Y-m-d', strtotime('-1 day')),
        ]);

        return $clientId;
    }
}
