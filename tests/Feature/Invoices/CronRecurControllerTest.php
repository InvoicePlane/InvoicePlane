<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\AbstractTestCase;

class CronRecurControllerTest extends AbstractTestCase
{
    #[Test]
    public function it_returns_500_for_a_wrong_cron_key(): void
    {
        /* Arrange: show_error(..., 500) is outside request.php's 400-499
         * "treat as HTTP response" range, so it surfaces as an exception. */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'cron_key', 'setting_value' => 'the-real-key']);
        $seeded = $this->seedRecurringInvoice();

        /* Act */
        try {
            $this->get('/invoices/cron/recur/wrong-key');
            self::fail('Expected a 500 for a wrong cron key.');
        } catch (RuntimeException $exception) {
            /* Assert */
            self::assertStringContainsString('Wrong cron key provided', $exception->getMessage());
        }

        $this->assertDatabaseCount('ip_invoices', 1, ['client_id' => $seeded['clientId']]);
    }

    #[Test]
    public function it_returns_500_for_a_missing_cron_key(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'cron_key', 'setting_value' => 'the-real-key']);

        /* Act */
        try {
            $this->get('/invoices/cron/recur');
            self::fail('Expected a 500 for a missing cron key.');
        } catch (RuntimeException $exception) {
            /* Assert */
            self::assertStringContainsString('Wrong cron key provided', $exception->getMessage());
        }
    }

    #[Test]
    public function it_generates_a_due_recurring_invoice_with_the_correct_cron_key(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'cron_key', 'setting_value' => 'the-real-key']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoices_due_after', 'setting_value' => '30']);
        $seeded = $this->seedRecurringInvoice();

        /* Act */
        $response = $this->get('/invoices/cron/recur/the-real-key');

        /* Assert */
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertDatabaseCount('ip_invoices', 2, ['client_id' => $seeded['clientId']]);
        $this->assertDatabaseMissing('ip_invoices_recurring', ['invoice_recurring_id' => $seeded['recurringId'], 'recur_next_date' => date('Y-m-d', strtotime('-1 day'))]);
    }

    #[Test]
    public function it_does_not_generate_an_invoice_for_a_not_yet_due_recurring_invoice(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'cron_key', 'setting_value' => 'the-real-key']);
        $seeded = $this->seedRecurringInvoice(['recur_next_date' => date('Y-m-d', strtotime('+10 days'))]);

        /* Act */
        $response = $this->get('/invoices/cron/recur/the-real-key');

        /* Assert */
        $this->assertDatabaseCount('ip_invoices', 1, ['client_id' => $seeded['clientId']]);
    }

    #[Test]
    public function it_does_not_generate_an_invoice_for_an_expired_recurring_series(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'cron_key', 'setting_value' => 'the-real-key']);
        $seeded = $this->seedRecurringInvoice(['recur_end_date' => date('Y-m-d', strtotime('-1 day'))]);

        /* Act */
        $response = $this->get('/invoices/cron/recur/the-real-key');

        /* Assert */
        $this->assertDatabaseCount('ip_invoices', 1, ['client_id' => $seeded['clientId']]);
    }

    private function seedRecurringInvoice(array $overrides = []): array
    {
        $clientId    = $this->seedClient();
        $invoiceId   = $this->seedInvoice($clientId, ['invoice_number' => 'CRON-SRC-' . bin2hex(random_bytes(4))]);
        $recurringId = $this->databaseInsert('ip_invoices_recurring', array_merge([
            'invoice_id'       => $invoiceId,
            'recur_start_date' => date('Y-m-d', strtotime('-1 day')),
            'recur_end_date'   => null,
            'recur_frequency'  => '1M',
            'recur_next_date'  => date('Y-m-d', strtotime('-1 day')),
        ], $overrides));

        return ['clientId' => $clientId, 'invoiceId' => $invoiceId, 'recurringId' => $recurringId];
    }
}
