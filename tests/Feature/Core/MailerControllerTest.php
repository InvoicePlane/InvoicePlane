<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Tests\Feature\Core\MailerController::class)]
class MailerControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    protected $user;

    protected tmpClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user   = $this->seedModel('User', ['user_type' => 1, 'user_active' => 1]);
        $this->client = $this->seedModel('tmpClient', ['client_email' => 'client@example.com']);
        $this->actingAs($this->user);

        // Mock mailer as configured
        Config::set('mail.driver', 'smtp');
    }

    #[Test]
    public function it_displays_invoice_mail_composer(): void
    {
        $invoice       = $this->seedModel('Invoice', ['client_id' => $this->client->client_id]);
        $emailTemplate = $this->seedModel('EmailTemplate', ['email_template_type' => 'invoice']);

        $response = $this->get(route('mailer.invoice', ['invoice_id' => $invoice->invoice_id]));

        $response->assertSuccessful();
        $response->assertViewHas('invoice');
        $response->assertViewHas('email_templates');
        $response->assertViewHas('pdf_templates');
        $response->assertViewHas('custom_fields');
    }

    #[Test]
    public function it_displays_quote_mail_composer(): void
    {
        $quote         = $this->seedModel('Quote', ['client_id' => $this->client->client_id]);
        $emailTemplate = $this->seedModel('EmailTemplate', ['email_template_type' => 'quote']);

        $response = $this->get(route('mailer.quote', ['quote_id' => $quote->quote_id]));

        $response->assertSuccessful();
        $response->assertViewHas('quote');
        $response->assertViewHas('email_templates');
        $response->assertViewHas('pdf_templates');
    }

    #[Test]
    public function it_returns_503_when_mailer_not_configured(): void
    {
        Config::set('mail.driver', null);

        $invoice = $this->seedModel('Invoice');

        $response = $this->get(route('mailer.invoice', ['invoice_id' => $invoice->invoice_id]));

        $response->assertStatus(503);
        $response->assertViewIs('mailer.not_configured');
    }

    #[Test]
    public function it_sends_invoice_email_with_pdf(): void
    {
        Mail::fake();

        $invoice = $this->seedModel('Invoice', [
            'client_id'      => $this->client->client_id,
            'invoice_number' => 'INV-001',
        ]);

        $emailData = [
            'to_email'     => 'client@example.com',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Invoice',
            'body'         => 'Please find attached your invoice.',
            'pdf_template' => 'default',
            'cc'           => '',
            'bcc'          => '',
        ];

        $response = $this->post(route('mailer.sendInvoice', ['invoice_id' => $invoice->invoice_id]), $emailData);

        $response->assertRedirect(route('invoices.view', ['invoice_id' => $invoice->invoice_id]));
        $response->assertSessionHas('alert_success');

        Mail::assertSent(function ($mail) use ($emailData) {
            return $mail->hasTo($emailData['to_email']);
        });
    }

    #[Test]
    public function it_sends_invoice_email_with_cc_and_bcc(): void
    {
        Mail::fake();

        $invoice = $this->seedModel('Invoice', [
            'client_id'      => $this->client->client_id,
            'invoice_number' => 'INV-002',
        ]);

        $emailData = [
            'to_email'     => 'client@example.com',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Invoice',
            'body'         => 'Invoice email body',
            'pdf_template' => 'default',
            'cc'           => 'cc@example.com',
            'bcc'          => 'bcc@example.com',
        ];

        $response = $this->post(route('mailer.sendInvoice', ['invoice_id' => $invoice->invoice_id]), $emailData);

        $response->assertRedirect(route('invoices.view', ['invoice_id' => $invoice->invoice_id]));

        Mail::assertSent(function ($mail) use ($emailData) {
            return $mail->hasCc($emailData['cc']) && $mail->hasBcc($emailData['bcc']);
        });
    }

    #[Test]
    public function it_converts_plain_text_to_html_in_email_body(): void
    {
        Mail::fake();

        $invoice = $this->seedModel('Invoice', [
            'client_id'      => $this->client->client_id,
            'invoice_number' => 'INV-003',
        ]);

        $emailData = [
            'to_email'     => 'client@example.com',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Invoice',
            'body'         => "Line 1\nLine 2\nLine 3", // Plain text with newlines
            'pdf_template' => 'default',
        ];

        $response = $this->post(route('mailer.sendInvoice', ['invoice_id' => $invoice->invoice_id]), $emailData);

        $response->assertRedirect(route('invoices.view', ['invoice_id' => $invoice->invoice_id]));

        // Body should have been converted with nl2br
        Mail::assertSent(function ($mail) {
            return str_contains($mail->body, '<br');
        });
    }

    #[Test]
    public function it_attaches_invoice_uploads_to_email(): void
    {
        Mail::fake();

        $invoice = $this->seedModel('Invoice', [
            'client_id'      => $this->client->client_id,
            'invoice_number' => 'INV-004',
        ]);

        $this->seedModelMany('Upload', 2, [
            'invoice_id' => $invoice->invoice_id,
        ]);

        $emailData = [
            'to_email'     => 'client@example.com',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Invoice',
            'body'         => 'Invoice with attachments',
            'pdf_template' => 'default',
        ];

        $response = $this->post(route('mailer.sendInvoice', ['invoice_id' => $invoice->invoice_id]), $emailData);

        $response->assertRedirect(route('invoices.view', ['invoice_id' => $invoice->invoice_id]));
    }

    #[Test]
    public function it_generates_invoice_number_before_sending_email(): void
    {
        Mail::fake();

        $invoice = $this->seedModel('Invoice', [
            'client_id'         => $this->client->client_id,
            'invoice_number'    => null,
            'invoice_status_id' => 1,
        ]);

        $emailData = [
            'to_email'     => 'client@example.com',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Invoice',
            'body'         => 'Invoice email',
            'pdf_template' => 'default',
        ];

        $response = $this->post(route('mailer.sendInvoice', ['invoice_id' => $invoice->invoice_id]), $emailData);

        $invoice->refresh();
        $this->assertNotNull($invoice->invoice_number);
    }

    #[Test]
    public function it_marks_invoice_as_sent_after_emailing(): void
    {
        Mail::fake();

        $invoice = $this->seedModel('Invoice', [
            'client_id'         => $this->client->client_id,
            'invoice_status_id' => 1,
        ]);

        $emailData = [
            'to_email'     => 'client@example.com',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Invoice',
            'body'         => 'Invoice email',
            'pdf_template' => 'default',
        ];

        $response = $this->post(route('mailer.sendInvoice', ['invoice_id' => $invoice->invoice_id]), $emailData);

        $invoice->refresh();
        $this->assertEquals(2, $invoice->invoice_status_id); // Sent status
    }

    #[Test]
    public function it_cancels_invoice_email_and_redirects_to_view(): void
    {
        $invoice = $this->seedModel('Invoice');

        $response = $this->post(route('mailer.sendInvoice', ['invoice_id' => $invoice->invoice_id]), [
            'btn_cancel' => true,
        ]);

        $response->assertRedirect(route('invoices.view', ['invoice_id' => $invoice->invoice_id]));
    }

    #[Test]
    public function it_redirects_to_mailer_form_on_failed_email_send(): void
    {
        Mail::fake();
        Mail::shouldReceive('send')->andThrow(new Exception('Mail server error'));

        $invoice = $this->seedModel('Invoice', [
            'client_id' => $this->client->client_id,
        ]);

        $emailData = [
            'to_email'     => 'invalid-email',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Invoice',
            'body'         => 'Invoice email',
            'pdf_template' => 'default',
        ];

        $response = $this->post(route('mailer.sendInvoice', ['invoice_id' => $invoice->invoice_id]), $emailData);

        $response->assertRedirect(route('mailer.invoice', ['invoice_id' => $invoice->invoice_id]));
    }

    #[Test]
    public function it_sends_quote_email_with_pdf(): void
    {
        Mail::fake();

        $quote = $this->seedModel('Quote', [
            'client_id'    => $this->client->client_id,
            'quote_number' => 'QUO-001',
        ]);

        $emailData = [
            'to_email'     => 'client@example.com',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Quote',
            'body'         => 'Please find attached your quote.',
            'pdf_template' => 'default',
            'cc'           => '',
            'bcc'          => '',
        ];

        $response = $this->post(route('mailer.sendQuote', ['quote_id' => $quote->quote_id]), $emailData);

        $response->assertRedirect(route('quotes.view', ['quote_id' => $quote->quote_id]));
        $response->assertSessionHas('alert_success');

        Mail::assertSent(function ($mail) use ($emailData) {
            return $mail->hasTo($emailData['to_email']);
        });
    }

    #[Test]
    public function it_generates_quote_number_before_sending_email(): void
    {
        Mail::fake();

        $quote = $this->seedModel('Quote', [
            'client_id'       => $this->client->client_id,
            'quote_number'    => null,
            'quote_status_id' => 1,
        ]);

        $emailData = [
            'to_email'     => 'client@example.com',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Quote',
            'body'         => 'Quote email',
            'pdf_template' => 'default',
        ];

        $response = $this->post(route('mailer.sendQuote', ['quote_id' => $quote->quote_id]), $emailData);

        $quote->refresh();
        $this->assertNotNull($quote->quote_number);
    }

    #[Test]
    public function it_marks_quote_as_sent_after_emailing(): void
    {
        Mail::fake();

        $quote = $this->seedModel('Quote', [
            'client_id'       => $this->client->client_id,
            'quote_status_id' => 1,
        ]);

        $emailData = [
            'to_email'     => 'client@example.com',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Quote',
            'body'         => 'Quote email',
            'pdf_template' => 'default',
        ];

        $response = $this->post(route('mailer.sendQuote', ['quote_id' => $quote->quote_id]), $emailData);

        $quote->refresh();
        $this->assertEquals(2, $quote->quote_status_id); // Sent status
    }

    #[Test]
    public function it_cancels_quote_email_and_redirects_to_view(): void
    {
        $quote = $this->seedModel('Quote');

        $response = $this->post(route('mailer.sendQuote', ['quote_id' => $quote->quote_id]), [
            'btn_cancel' => true,
        ]);

        $response->assertRedirect(route('quotes.view', ['quote_id' => $quote->quote_id]));
    }

    #[Test]
    public function it_attaches_quote_uploads_to_email(): void
    {
        Mail::fake();

        $quote = $this->seedModel('Quote', [
            'client_id'    => $this->client->client_id,
            'quote_number' => 'QUO-002',
        ]);

        $this->seedModelMany('Upload', 2, [
            'quote_id' => $quote->quote_id,
        ]);

        $emailData = [
            'to_email'     => 'client@example.com',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Quote',
            'body'         => 'Quote with attachments',
            'pdf_template' => 'default',
        ];

        $response = $this->post(route('mailer.sendQuote', ['quote_id' => $quote->quote_id]), $emailData);

        $response->assertRedirect(route('quotes.view', ['quote_id' => $quote->quote_id]));
    }

    #[Test]
    public function it_decodes_html_entities_in_email_body(): void
    {
        Mail::fake();

        $invoice = $this->seedModel('Invoice', [
            'client_id' => $this->client->client_id,
        ]);

        $emailData = [
            'to_email'     => 'client@example.com',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Invoice',
            'body'         => '<p>Invoice &amp; details</p>',
            'pdf_template' => 'default',
        ];

        $response = $this->post(route('mailer.sendInvoice', ['invoice_id' => $invoice->invoice_id]), $emailData);

        $response->assertRedirect(route('invoices.view', ['invoice_id' => $invoice->invoice_id]));

        // HTML entities should be decoded
        Mail::assertSent(function ($mail) {
            return str_contains($mail->body, '&') && ! str_contains($mail->body, '&amp;');
        });
    }
}
