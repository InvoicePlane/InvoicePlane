<?php

namespace Tests\Feature\Regression;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * TDD Test Suite for #1497: SMTP sender regression
 *
 * Issue: When sending emails, the system uses the logged-in user's email
 * instead of the configured SMTP from address (settings.smtp_mail_from).
 *
 * Expected Behavior: When smtp_mail_from is configured in settings,
 * that address should be used as the default "from" email for sent invoices/quotes,
 * NOT the user's email address.
 *
 * Root Cause: The Mailer controller doesn't properly use get_setting('smtp_mail_from')
 * when initializing the email form, or the email sending uses the wrong fallback.
 */
class Issue1497SmtpSenderTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();

        // Configure email method so mailer is available
        $this->databaseInsertOrIgnore('ip_settings', [
            'setting_key' => 'email_send_method',
            'setting_value' => 'phpmail',
        ]);
    }

    #[Test]
    public function mailer_uses_smtp_mail_from_setting_as_default(): void
    {
        /* Arrange - Set smtp_mail_from in settings */
        $this->databaseInsertOrIgnore('ip_settings', [
            'setting_key' => 'smtp_mail_from',
            'setting_value' => 'noreply@company.com',
        ]);

        $clientId = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        /* Act - Access the mailer form for this invoice */
        $response = $this->get('/mailer/invoice/' . $invoiceId);

        /* Assert - The from_email field should be prepopulated with smtp_mail_from, not user email */
        $this->assertResponseStatusCode($response, 200);
        self::assertStringContainsString(
            'value="noreply@company.com"',
            $response->body(),
            'Mailer form should prepopulate from_email with smtp_mail_from setting'
        );

        // Also verify user email is NOT in the from_email field
        self::assertStringNotContainsString(
            'value="admin@test.local"',
            $response->body(),
            'Mailer form should NOT show user email when smtp_mail_from is configured'
        );
    }

    #[Test]
    public function mailer_falls_back_to_user_email_when_smtp_mail_from_empty(): void
    {
        /* Arrange - Ensure smtp_mail_from is NOT set */
        $this->databaseDelete('ip_settings', ['setting_key' => 'smtp_mail_from']);

        $clientId = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        /* Act - Access the mailer form for this invoice */
        $response = $this->get('/mailer/invoice/' . $invoiceId);

        /* Assert - The from_email field should fall back to user email */
        $this->assertResponseStatusCode($response, 200);
        self::assertStringContainsString(
            'value="admin@test.local"',
            $response->body(),
            'Mailer form should fall back to user email when smtp_mail_from is not set'
        );
    }
}
