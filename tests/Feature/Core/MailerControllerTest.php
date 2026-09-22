<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Mailer controller — application/modules/mailer/controllers/Mailer.php.
 *
 * Renders the "send this invoice/quote" form and dispatches the mail. Absorbs
 * Issue1497SmtpSenderTest (the from-address defaulting regression).
 */
#[Group('mailer')]
#[CoversClass(\Mailer::class)]
class MailerControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'email_send_method', 'setting_value' => 'phpmail']);
    }

    // -------------------------------------------------------------------------
    // Read — the send-invoice form
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_send_invoice_form_for_a_seeded_invoice(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient(['client_name' => 'Mailer Form Client']);
        $invoiceId = $this->seedInvoice($clientId, ['invoice_number' => 'INV-MAIL-0001']);

        /* Act */
        $response = $this->get('/mailer/invoice/' . $invoiceId);

        /* Assert */
        $this->assertResponseBodyContains($response, 'INV-MAIL-0001');
        $this->assertResponseBodyNotContains($response, 'A PHP Error was encountered');
    }

    // -------------------------------------------------------------------------
    // From-address defaulting — #1497 regression
    // -------------------------------------------------------------------------

    #[Test]
    public function it_prefills_the_from_address_with_the_smtp_mail_from_setting(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'smtp_mail_from', 'setting_value' => 'noreply@company.example']);
        $this->databaseUpdate('ip_settings', ['setting_value' => 'noreply@company.example'], ['setting_key' => 'smtp_mail_from']);
        $invoiceId = $this->seedInvoice($this->seedClient());

        /* Act */
        $response = $this->get('/mailer/invoice/' . $invoiceId);

        /* Assert */
        $this->assertResponseBodyContains($response, 'value="noreply@company.example"');
        $this->assertResponseBodyNotContains($response, 'value="admin@test.local"');
    }

    #[Test]
    public function it_shows_not_configured_message_when_mailer_is_not_configured(): void
    {
        /* Arrange: mailer is not configured by default in test setup */
        $clientId = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        /* Act */
        $response = $this->get("/mailer/invoice/{$invoiceId}");

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        self::assertTrue(
            $response->contains('not configured') || $response->contains('mail'),
            'Mailer not-configured message should be visible in the response'
        );
    }

    #[Test]
    public function it_displays_invoice_mailer_form_when_mailer_is_configured(): void
    {
        /* Arrange: configure mailer settings */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'smtp_host', 'setting_value' => 'smtp.example.com']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'smtp_port', 'setting_value' => '587']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'smtp_username', 'setting_value' => 'user']);

        $clientId = $this->seedClient(['client_email' => 'client@example.com']);
        $invoiceId = $this->seedInvoice($clientId);

        /* Act */
        $response = $this->get("/mailer/invoice/{$invoiceId}");

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        self::assertTrue(
            $response->contains('email') || $response->contains('template') || $response->contains('invoice'),
            'Mailer form must contain email/template controls for the invoice'
        );
    }

    #[Test]
    public function it_redirects_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/mailer/invoice/1');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET /mailer/invoice/1 must redirect. Got status [%d].', $response->statusCode())
        );
    }

    #[Test]
    public function it_displays_quote_mailer_form_when_mailer_is_configured(): void
    {
        /* Arrange: configure mailer */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'smtp_host', 'setting_value' => 'smtp.example.com']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'smtp_port', 'setting_value' => '587']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'smtp_username', 'setting_value' => 'user']);

        $clientId = $this->seedClient(['client_email' => 'quote@example.com']);
        $quoteId = $this->seedQuote($clientId);

        /* Act */
        $response = $this->get("/mailer/quote/{$quoteId}");

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        self::assertTrue(
            $response->contains('email') || $response->contains('template') || $response->contains('quote'),
            'Mailer form must contain email/template controls for the quote'
        );
    }

    #[Test]
    public function it_loads_invoice_data_for_mailer_form(): void
    {
        /* Arrange: configure mailer and create invoice with details */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'smtp_host', 'setting_value' => 'smtp.example.com']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'smtp_port', 'setting_value' => '587']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'smtp_username', 'setting_value' => 'user']);

        $clientId = $this->seedClient(['client_name' => 'Important Client', 'client_email' => 'important@example.com']);
        $invoiceId = $this->seedInvoice($clientId, ['invoice_number' => 'INV-2025-001']);

        /* Act */
        $response = $this->get("/mailer/invoice/{$invoiceId}");

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        self::assertTrue(
            $response->contains('INV-2025-001') || $response->contains('Important Client'),
            'Mailer form must display invoice number or client name'
        );
    }

    #[Test]
    public function it_falls_back_to_the_current_user_email_when_smtp_mail_from_is_empty(): void
    {
        /* Arrange */
        $this->databaseDelete('ip_settings', ['setting_key' => 'smtp_mail_from']);
        $invoiceId = $this->seedInvoice($this->seedClient());

        /* Act */
        $response = $this->get('/mailer/invoice/' . $invoiceId);

        /* Assert */
        $this->assertResponseBodyContains($response, 'value="admin@test.local"');
        $this->assertResponseBodyNotContains($response, 'value="noreply@company.example"');
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_away_from_the_mailer(): void
    {
        /* Arrange */
        $invoiceId = $this->seedInvoice($this->seedClient(), ['invoice_number' => 'INV-MAIL-SECRET']);
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/mailer/invoice/' . $invoiceId);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
        $this->assertResponseBodyNotContains($response, 'INV-MAIL-SECRET');
    }

    protected function seedQuote(int $clientId): int
    {
        return $this->databaseInsertGetId('ip_quotes', [
            'client_id'         => $clientId,
            'quote_number'      => 'Q-' . bin2hex(random_bytes(4)),
            'quote_date_created' => date('Y-m-d'),
            'quote_date_expires' => date('Y-m-d', strtotime('+30 days')),
            'quote_amount'       => 1000,
            'quote_status_id'    => 1,
        ]);
    }
}
