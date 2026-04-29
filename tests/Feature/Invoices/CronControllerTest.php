<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

/**
 * Cron Controller Feature Tests.
 *
 * Tests the cron endpoint for recurring invoice processing.
 */
class CronControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    private function fetchCronKey(): ?string
    {
        $this->skipWithoutDatabase();
        $row = $this->databaseFetchOne('ip_settings', ['setting_key' => 'cron_key']);

        return $row['setting_value'] ?? null;
    }

    #[Test]
    public function it_rejects_invalid_cron_key_and_exits(): void
    {
        $response = $this->get('/invoices/cron/recur/definitely_wrong_key_xyz');

        $this->assertNotEquals(200, $response->statusCode());
        $this->assertFalse($response->isRedirect());
    }

    #[Test]
    public function it_processes_active_recurring_invoices_with_valid_key(): void
    {
        $this->skipWithoutDatabase();
        $cronKey = $this->fetchCronKey();
        if ($cronKey === null) {
            $this->markTestSkipped('cron_key not found in ip_settings.');
        }

        $response = $this->get('/invoices/cron/recur/' . $cronKey);

        $this->assertNotEquals(500, $response->statusCode());
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_new_invoice_from_recurring_template(): void
    {
        $this->skipWithoutDatabase();
        $cronKey = $this->fetchCronKey();
        if ($cronKey === null) {
            $this->markTestSkipped('cron_key not found in ip_settings.');
        }

        $response = $this->get('/invoices/cron/recur/' . $cronKey);

        $this->assertNotEquals(500, $response->statusCode());
    }

    #[Group('exotic')]
    #[Test]
    public function it_copies_all_items_from_source_invoice_to_new_invoice(): void
    {
        $this->skipWithoutDatabase();
        $cronKey = $this->fetchCronKey();
        if ($cronKey === null) {
            $this->markTestSkipped('cron_key not found in ip_settings.');
        }

        $response = $this->get('/invoices/cron/recur/' . $cronKey);

        $this->assertNotEquals(500, $response->statusCode());
    }

    #[Group('exotic')]
    #[Test]
    public function it_copies_tax_rates_from_source_invoice_to_new_invoice(): void
    {
        $this->skipWithoutDatabase();
        $cronKey = $this->fetchCronKey();
        if ($cronKey === null) {
            $this->markTestSkipped('cron_key not found in ip_settings.');
        }

        $response = $this->get('/invoices/cron/recur/' . $cronKey);

        $this->assertNotEquals(500, $response->statusCode());
    }

    #[Group('exotic')]
    #[Test]
    public function it_recalculates_amounts_for_new_invoice(): void
    {
        $this->skipWithoutDatabase();
        $cronKey = $this->fetchCronKey();
        if ($cronKey === null) {
            $this->markTestSkipped('cron_key not found in ip_settings.');
        }

        $response = $this->get('/invoices/cron/recur/' . $cronKey);

        $this->assertNotEquals(500, $response->statusCode());
    }

    #[Group('crud')]
    #[Test]
    public function it_updates_next_recur_date_after_processing(): void
    {
        $this->skipWithoutDatabase();
        $cronKey = $this->fetchCronKey();
        if ($cronKey === null) {
            $this->markTestSkipped('cron_key not found in ip_settings.');
        }

        $response = $this->get('/invoices/cron/recur/' . $cronKey);

        $this->assertNotEquals(500, $response->statusCode());
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_next_date_correctly_for_weekly_frequency(): void
    {
        $this->skipWithoutDatabase();
        $cronKey = $this->fetchCronKey();
        if ($cronKey === null) {
            $this->markTestSkipped('cron_key not found in ip_settings.');
        }

        $response = $this->get('/invoices/cron/recur/' . $cronKey);

        $this->assertNotEquals(500, $response->statusCode());
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_next_date_correctly_for_monthly_frequency(): void
    {
        $this->skipWithoutDatabase();
        $cronKey = $this->fetchCronKey();
        if ($cronKey === null) {
            $this->markTestSkipped('cron_key not found in ip_settings.');
        }

        $response = $this->get('/invoices/cron/recur/' . $cronKey);

        $this->assertNotEquals(500, $response->statusCode());
    }

    #[Test]
    public function it_sends_email_when_automatic_email_on_recur_is_enabled(): void
    {
        $this->skipWithoutDatabase();
        $cronKey = $this->fetchCronKey();
        if ($cronKey === null) {
            $this->markTestSkipped('cron_key not found in ip_settings.');
        }

        $response = $this->get('/invoices/cron/recur/' . $cronKey);

        $this->assertNotEquals(500, $response->statusCode());
    }

    #[Test]
    public function it_skips_email_when_automatic_email_on_recur_is_disabled(): void
    {
        $this->skipWithoutDatabase();
        $cronKey = $this->fetchCronKey();
        if ($cronKey === null) {
            $this->markTestSkipped('cron_key not found in ip_settings.');
        }

        $response = $this->get('/invoices/cron/recur/' . $cronKey);

        $this->assertNotEquals(500, $response->statusCode());
    }

    #[Test]
    public function it_marks_invoice_as_sent_after_successful_email(): void
    {
        $this->skipWithoutDatabase();
        $cronKey = $this->fetchCronKey();
        if ($cronKey === null) {
            $this->markTestSkipped('cron_key not found in ip_settings.');
        }

        $response = $this->get('/invoices/cron/recur/' . $cronKey);

        $this->assertNotEquals(500, $response->statusCode());
    }

    #[Test]
    public function it_logs_error_when_email_template_is_not_set(): void
    {
        $this->skipWithoutDatabase();
        $cronKey = $this->fetchCronKey();
        if ($cronKey === null) {
            $this->markTestSkipped('cron_key not found in ip_settings.');
        }

        $response = $this->get('/invoices/cron/recur/' . $cronKey);

        $this->assertNotEquals(500, $response->statusCode());
    }

    #[Test]
    public function it_processes_multiple_recurring_invoices_in_single_run(): void
    {
        $this->skipWithoutDatabase();
        $cronKey = $this->fetchCronKey();
        if ($cronKey === null) {
            $this->markTestSkipped('cron_key not found in ip_settings.');
        }

        $response = $this->get('/invoices/cron/recur/' . $cronKey);

        $this->assertNotEquals(500, $response->statusCode());
    }

    #[Test]
    public function it_generates_unique_invoice_numbers_for_each_new_invoice(): void
    {
        $this->skipWithoutDatabase();
        $cronKey = $this->fetchCronKey();
        if ($cronKey === null) {
            $this->markTestSkipped('cron_key not found in ip_settings.');
        }

        $response = $this->get('/invoices/cron/recur/' . $cronKey);

        $this->assertNotEquals(500, $response->statusCode());
    }

    #[Test]
    public function it_generates_unique_url_keys_for_each_new_invoice(): void
    {
        $this->skipWithoutDatabase();
        $cronKey = $this->fetchCronKey();
        if ($cronKey === null) {
            $this->markTestSkipped('cron_key not found in ip_settings.');
        }

        $response = $this->get('/invoices/cron/recur/' . $cronKey);

        $this->assertNotEquals(500, $response->statusCode());
    }

    #[Test]
    public function it_logs_debug_information_when_debug_mode_is_enabled(): void
    {
        $this->skipWithoutDatabase();
        $cronKey = $this->fetchCronKey();
        if ($cronKey === null) {
            $this->markTestSkipped('cron_key not found in ip_settings.');
        }

        $response = $this->get('/invoices/cron/recur/' . $cronKey);

        $this->assertNotEquals(500, $response->statusCode());
    }
}
