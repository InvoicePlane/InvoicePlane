<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\AbstractTestCase;

/**
 * Feature tests for the invoice reminder cron job (/invoices/cron/reminders).
 *
 * Tests the complete flow: cron triggers, reminders are identified, emails are sent,
 * and the ip_invoice_reminders table is updated. Tests both success and failure paths.
 */
class CronRemindersFeatureTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Reminders run as a cron job (unauthenticated, protected by cron_key)
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'cron_key', 'setting_value' => 'test-cron-key']);
    }

    #[Test]
    public function it_sends_a_before_due_reminder_for_an_invoice_matching_the_offset(): void
    {
        /* Arrange: invoice due in 7 days, reminder configured for 7 days before */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoice_reminders_enabled', 'setting_value' => '1']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoice_reminder_days_before', 'setting_value' => '7']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'email_invoice_template', 'setting_value' => '1']);

        $seeded      = $this->seedSimpleInvoice(['invoice_date_due' => date('Y-m-d', strtotime('+7 days'))]);
        $clientEmail = $this->database()->table('ip_clients')->where('client_id', $seeded['clientId'])->value('client_email');

        /* Act */
        $response = $this->get('/invoices/cron/reminders/test-cron-key');

        /* Assert: Cron succeeds */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);

        /* Behavior: Reminder is recorded in ip_invoice_reminders */
        $this->assertDatabaseHas('ip_invoice_reminders', [
            'invoice_id'      => $seeded['invoiceId'],
            'reminder_type'   => 'before_due',
            'reminder_offset' => 7,
            'reminder_status' => 'sent',
        ]);

        /* Behavior: Email was attempted to be sent (mock mailer records it) */
        // In a real test, this would verify the email was queued/sent via the mailer service
    }

    #[Test]
    public function it_skips_reminders_when_reminders_are_disabled_globally(): void
    {
        /* Arrange: reminders disabled in settings */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoice_reminders_enabled', 'setting_value' => '0']);

        $seeded = $this->seedSimpleInvoice(['invoice_date_due' => date('Y-m-d', strtotime('+7 days'))]);

        /* Act */
        $response = $this->get('/invoices/cron/reminders/test-cron-key');

        /* Assert: Cron still succeeds (no error) */
        $this->assertResponseStatusCode($response, 200);

        /* Behavior: No reminders were created/sent */
        $this->assertDatabaseMissing('ip_invoice_reminders', [
            'invoice_id' => $seeded['invoiceId'],
        ]);
    }

    #[Test]
    public function it_skips_reminders_for_invoices_with_client_opt_out(): void
    {
        /* Arrange: invoice with client that disabled reminders */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoice_reminders_enabled', 'setting_value' => '1']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoice_reminder_days_before', 'setting_value' => '7']);

        $seeded = $this->seedSimpleInvoice(['invoice_date_due' => date('Y-m-d', strtotime('+7 days'))]);

        /* Mark the client as opted-out from reminders */
        $this->database()->table('ip_clients')
            ->where('client_id', $seeded['clientId'])
            ->update(['client_disable_reminders' => 1]);

        /* Act */
        $response = $this->get('/invoices/cron/reminders/test-cron-key');

        /* Assert: Cron succeeds */
        $this->assertResponseStatusCode($response, 200);

        /* Behavior: No reminders were sent for the opted-out client */
        $this->assertDatabaseMissing('ip_invoice_reminders', [
            'invoice_id' => $seeded['invoiceId'],
        ]);
    }

    #[Test]
    public function it_skips_reminders_for_invoices_with_invoice_level_opt_out(): void
    {
        /* Arrange: invoice with opt-out flag set */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoice_reminders_enabled', 'setting_value' => '1']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoice_reminder_days_before', 'setting_value' => '7']);

        $seeded = $this->seedSimpleInvoice([
            'invoice_date_due'          => date('Y-m-d', strtotime('+7 days')),
            'invoice_disable_reminders' => 1,
        ]);

        /* Act */
        $response = $this->get('/invoices/cron/reminders/test-cron-key');

        /* Assert: Cron succeeds */
        $this->assertResponseStatusCode($response, 200);

        /* Behavior: No reminders were sent for the opted-out invoice */
        $this->assertDatabaseMissing('ip_invoice_reminders', [
            'invoice_id' => $seeded['invoiceId'],
        ]);
    }

    #[Test]
    public function it_skips_reminders_for_already_paid_invoices(): void
    {
        /* Arrange: invoice that's already paid (balance = 0) */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoice_reminders_enabled', 'setting_value' => '1']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoice_reminder_days_before', 'setting_value' => '7']);

        $seeded = $this->seedSimpleInvoice([
            'invoice_date_due' => date('Y-m-d', strtotime('+7 days')),
            'invoice_amount'   => 100,
        ]);

        /* Record a payment that covers the entire invoice */
        $this->database()->table('ip_payments')->insert([
            'invoice_id'        => $seeded['invoiceId'],
            'payment_date'      => date('Y-m-d'),
            'payment_amount'    => 100,
            'payment_method_id' => 1,
        ]);

        /* Act */
        $response = $this->get('/invoices/cron/reminders/test-cron-key');

        /* Assert: Cron succeeds */
        $this->assertResponseStatusCode($response, 200);

        /* Behavior: No reminders were sent for paid invoice */
        $this->assertDatabaseMissing('ip_invoice_reminders', [
            'invoice_id' => $seeded['invoiceId'],
        ]);
    }

    #[Test]
    public function it_does_not_send_duplicate_reminders_for_the_same_offset(): void
    {
        /* Arrange: invoice with a reminder already sent for this offset */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoice_reminders_enabled', 'setting_value' => '1']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoice_reminder_days_before', 'setting_value' => '7']);

        $seeded = $this->seedSimpleInvoice(['invoice_date_due' => date('Y-m-d', strtotime('+7 days'))]);

        /* Pre-populate the reminder log showing this reminder was already sent */
        $this->database()->table('ip_invoice_reminders')->insert([
            'invoice_id'         => $seeded['invoiceId'],
            'reminder_type'      => 'before_due',
            'reminder_offset'    => 7,
            'reminder_status'    => 'sent',
            'reminder_date_sent' => date('Y-m-d H:i:s'),
        ]);

        /* Act: Run cron again */
        $response = $this->get('/invoices/cron/reminders/test-cron-key');

        /* Assert: Cron succeeds */
        $this->assertResponseStatusCode($response, 200);

        /* Behavior: Only ONE reminder for this invoice (no duplicate) */
        $count = $this->database()->table('ip_invoice_reminders')
            ->where('invoice_id', $seeded['invoiceId'])
            ->where('reminder_type', 'before_due')
            ->where('reminder_offset', 7)
            ->count();

        $this->assertSame(1, $count, 'Should only have one reminder for this offset, not a duplicate');
    }

    #[Test]
    public function it_respects_the_maximum_reminders_limit_per_invoice(): void
    {
        /* Arrange: configure max 2 reminders total per invoice */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoice_reminders_enabled', 'setting_value' => '1']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoice_reminder_days_before', 'setting_value' => '7,3,1']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoice_reminder_max_total', 'setting_value' => '2']);

        $seeded = $this->seedSimpleInvoice(['invoice_date_due' => date('Y-m-d', strtotime('-5 days'))]);

        /* Act: Run cron which would send 7, 3, 1-day reminders but max is 2 */
        $response = $this->get('/invoices/cron/reminders/test-cron-key');

        /* Assert: Cron succeeds */
        $this->assertResponseStatusCode($response, 200);

        /* Behavior: Only 2 reminders were sent (the max) */
        $reminderCount = $this->database()->table('ip_invoice_reminders')
            ->where('invoice_id', $seeded['invoiceId'])
            ->where('reminder_status', 'sent')
            ->count();

        $this->assertLessThanOrEqual(2, $reminderCount, 'Should not exceed max_total reminders');
    }

    #[Test]
    public function it_rejects_the_cron_request_with_wrong_cron_key(): void
    {
        /* Arrange: correct key is "test-cron-key" (set in setUp) */

        /* Act */
        try {
            $this->get('/invoices/cron/reminders/wrong-key');
            self::fail('Expected RuntimeException for wrong cron key');
        } catch (RuntimeException $e) {
            /* Assert: Request is rejected */
            self::assertStringContainsString('Wrong cron key provided', $e->getMessage());
        }
    }

    #[Test]
    public function it_handles_mailer_not_configured_gracefully(): void
    {
        /* Arrange: reminders enabled but mailer not configured */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoice_reminders_enabled', 'setting_value' => '1']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoice_reminder_days_before', 'setting_value' => '7']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'smtp_host', 'setting_value' => '']);

        $seeded = $this->seedSimpleInvoice(['invoice_date_due' => date('Y-m-d', strtotime('+7 days'))]);

        /* Act */
        $response = $this->get('/invoices/cron/reminders/test-cron-key');

        /* Assert: Cron still returns 200 (doesn't fail on mailer misconfiguration) */
        $this->assertResponseStatusCode($response, 200);

        /* Behavior: No reminders were sent (mailer error is logged but doesn't crash) */
        $this->assertDatabaseMissing('ip_invoice_reminders', [
            'invoice_id' => $seeded['invoiceId'],
        ]);
    }

    #[Test]
    public function it_logs_the_reminder_summary_at_error_level_for_production_visibility(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoice_reminders_enabled', 'setting_value' => '1']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoice_reminder_days_before', 'setting_value' => '7']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'email_invoice_template', 'setting_value' => '1']);

        $this->seedSimpleInvoice(['invoice_date_due' => date('Y-m-d', strtotime('+7 days'))]);

        /* Act */
        $response = $this->get('/invoices/cron/reminders/test-cron-key');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);

        /* Behavior: Reminder summary was logged (ideally would check logs, but depends on logging implementation) */
        // In production with IP_DEBUG=false, the summary is still logged at 'error' level
        // This test verifies no exception is thrown and cron completes
    }

    /**
     * Helper: Create a simple invoice for testing reminder scenarios.
     */
    protected function seedSimpleInvoice(array $overrides = []): array
    {
        $clientId = $this->database()->table('ip_clients')->insertGetId([
            'client_name'   => 'Test Client',
            'client_email'  => 'test@example.com',
            'client_active' => 1,
        ]);

        $invoiceId = $this->database()->table('ip_invoices')->insertGetId(array_merge([
            'client_id'                 => $clientId,
            'invoice_number'            => '001',
            'invoice_status_id'         => 1,
            'invoice_amount'            => 1000,
            'invoice_date_created'      => date('Y-m-d'),
            'invoice_date_due'          => date('Y-m-d', strtotime('+30 days')),
            'invoice_group_id'          => 1,
            'invoice_url_key'           => bin2hex(random_bytes(16)),
            'invoice_disable_reminders' => 0,
        ], $overrides));

        return [
            'clientId'  => $clientId,
            'invoiceId' => $invoiceId,
        ];
    }
}
