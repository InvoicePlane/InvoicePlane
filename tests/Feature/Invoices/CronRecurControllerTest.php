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

    #[Test]
    public function it_claims_the_recurring_invoice_before_it_draws_a_number_from_the_invoice_group(): void
    {
        /* Arrange: a trigger that advances the schedule whenever the group counter moves, the way a
         * concurrent run that wins the claim would. The number may only be drawn once this run owns the
         * row; drawn first, the claim is lost, no invoice exists and the number is burned (GHSA-hvvh). */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'cron_key', 'setting_value' => 'the-real-key']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoices_due_after', 'setting_value' => '30']);
        $seeded = $this->seedRecurringInvoice();
        $before = (int) $this->databaseFetchOne('ip_invoice_groups', ['invoice_group_id' => 1])['invoice_group_next_id'];
        $this->databaseRunScript('DROP TRIGGER IF EXISTS ip_test_advance_recur');
        $this->databaseRunScript('CREATE TRIGGER ip_test_advance_recur AFTER UPDATE ON ip_invoice_groups FOR EACH ROW UPDATE ip_invoices_recurring SET recur_next_date = DATE_ADD(recur_next_date, INTERVAL 1 MONTH)');

        try {
            /* Act */
            $this->get('/invoices/cron/recur/the-real-key');
        } finally {
            $this->databaseRunScript('DROP TRIGGER IF EXISTS ip_test_advance_recur');
        }

        /* Assert: one new invoice, and the counter moved exactly once for it */
        $this->assertDatabaseCount('ip_invoices', 2, ['client_id' => $seeded['clientId']]);
        $after = (int) $this->databaseFetchOne('ip_invoice_groups', ['invoice_group_id' => 1])['invoice_group_next_id'];
        self::assertSame($before + 1, $after);
    }

    protected function seedRecurringInvoice(array $overrides = []): array
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
