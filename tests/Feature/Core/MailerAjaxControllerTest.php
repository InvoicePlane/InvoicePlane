<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * mailer/controllers/Mailer.php. The constructor guards every action behind
 * mailer_configured() (no SMTP settings seeded in the test DB, matching a
 * fresh, not-yet-configured install), so this covers that guard plus the
 * send_invoice()/send_quote() cancel-button early exit, which runs before
 * the guard and must work even when mail is unconfigured.
 */
class MailerAjaxControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_shows_the_not_configured_view_for_invoice(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        /* Act */
        $response = $this->get('/mailer/invoice/' . $invoiceId);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_shows_the_not_configured_view_for_quote(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $quoteId  = $this->databaseInsert('ip_quotes', [
            'user_id'            => 1, 'client_id' => $clientId, 'invoice_group_id' => 1, 'quote_status_id' => 2,
            'quote_date_created' => date('Y-m-d'), 'quote_date_modified' => date('Y-m-d H:i:s'),
            'quote_date_expires' => date('Y-m-d', strtotime('+30 days')),
            'quote_number'       => 'MAIL-Q-001', 'quote_url_key' => bin2hex(random_bytes(16)),
        ]);

        /* Act */
        $response = $this->get('/mailer/quote/' . $quoteId);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_redirects_on_cancel_for_send_invoice_even_when_unconfigured(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        /* Act */
        $response = $this->post('/mailer/send_invoice/' . $invoiceId, ['btn_cancel' => '1']);

        /* Assert */
        self::assertTrue($response->isRedirect());
    }

    #[Test]
    public function it_does_not_send_or_mark_an_invoice_sent_when_mailer_is_not_configured(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId, ['invoice_status_id' => 1]);

        /* Act */
        $this->post('/mailer/send_invoice/' . $invoiceId, [
            'to_email' => 'client@test.local', 'from_email' => 'admin@test.local',
            'subject'  => 'Invoice', 'body' => 'Please pay',
        ]);

        /* Assert */
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'invoice_status_id' => 1]);
    }

    #[Test]
    public function it_redirects_on_cancel_for_send_quote_even_when_unconfigured(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $quoteId  = $this->databaseInsert('ip_quotes', [
            'user_id'            => 1, 'client_id' => $clientId, 'invoice_group_id' => 1, 'quote_status_id' => 1,
            'quote_date_created' => date('Y-m-d'), 'quote_date_modified' => date('Y-m-d H:i:s'),
            'quote_date_expires' => date('Y-m-d', strtotime('+30 days')),
            'quote_number'       => 'MAIL-Q-CANCEL', 'quote_url_key' => bin2hex(random_bytes(16)),
        ]);

        /* Act */
        $response = $this->post('/mailer/send_quote/' . $quoteId, ['btn_cancel' => '1']);

        /* Assert */
        self::assertTrue($response->isRedirect());
    }

    #[Test]
    public function it_does_not_send_or_mark_a_quote_sent_when_mailer_is_not_configured(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $quoteId  = $this->databaseInsert('ip_quotes', [
            'user_id'            => 1, 'client_id' => $clientId, 'invoice_group_id' => 1, 'quote_status_id' => 1,
            'quote_date_created' => date('Y-m-d'), 'quote_date_modified' => date('Y-m-d H:i:s'),
            'quote_date_expires' => date('Y-m-d', strtotime('+30 days')),
            'quote_number'       => 'MAIL-Q-NOOP', 'quote_url_key' => bin2hex(random_bytes(16)),
        ]);

        /* Act */
        $this->post('/mailer/send_quote/' . $quoteId, [
            'to_email' => 'client@test.local', 'from_email' => 'admin@test.local',
            'subject'  => 'Quote', 'body' => 'Here is your quote',
        ]);

        /* Assert */
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $quoteId, 'quote_status_id' => 1]);
    }
}
