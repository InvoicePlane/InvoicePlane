<?php

namespace Tests\Unit\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\UsesCodeIgniterModels;

/**
 * GHSA-hvvh-fjx5-9j55: two cron runs reading the same due recurring invoice could both generate the invoice and send
 * the customer two emails. Mdl_invoices_recurring::claim_for_processing() is the gate: a single conditional UPDATE on
 * recur_next_date that only one caller can win. CronRecurControllerTest runs one cron at a time, so the losing branch
 * (a second caller holding the stale date) was never exercised.
 */
final class InvoicesRecurringClaimTest extends AbstractTestCase
{
    use UsesCodeIgniterModels;

    protected function tearDown(): void
    {
        $this->tearDownCodeIgniter();
        parent::tearDown();
    }

    #[Test]
    public function it_lets_the_first_caller_claim_a_due_row_and_advances_the_next_date_by_the_frequency(): void
    {
        /* Arrange */
        $id = $this->seedRecurring('2026-03-15', '1M');

        /* Act */
        $claimed = $this->model()->claim_for_processing($id, '2026-03-15', '1M');

        /* Assert */
        self::assertTrue($claimed);
        $this->assertDatabaseHas('ip_invoices_recurring', ['invoice_recurring_id' => $id, 'recur_next_date' => '2026-04-15']);
    }

    #[Test]
    public function it_refuses_a_second_caller_that_still_holds_the_stale_date(): void
    {
        /* Arrange: caller A already claimed (the date moved on); caller B read the row before that */
        $id    = $this->seedRecurring('2026-03-15', '1M');
        $model = $this->model();
        self::assertTrue($model->claim_for_processing($id, '2026-03-15', '1M'));

        /* Act */
        $second = $model->claim_for_processing($id, '2026-03-15', '1M');

        /* Assert: the loser generates nothing, and the date is advanced exactly once */
        self::assertFalse($second, 'A stale claimant must not also process the invoice.');
        $this->assertDatabaseHas('ip_invoices_recurring', ['invoice_recurring_id' => $id, 'recur_next_date' => '2026-04-15']);
    }

    #[Test]
    public function it_refuses_a_claim_for_a_row_that_does_not_exist(): void
    {
        /* Act + Assert */
        self::assertFalse($this->model()->claim_for_processing(987654, '2026-03-15', '1M'));
    }

    #[Test]
    public function it_advances_by_the_configured_frequency_unit(): void
    {
        /* Arrange */
        $id = $this->seedRecurring('2026-03-15', '1Y');

        /* Act */
        $claimed = $this->model()->claim_for_processing($id, '2026-03-15', '1Y');

        /* Assert */
        self::assertTrue($claimed);
        $this->assertDatabaseHas('ip_invoices_recurring', ['invoice_recurring_id' => $id, 'recur_next_date' => '2027-03-15']);
    }

    #[Test]
    public function it_refuses_a_claimant_whose_stale_date_is_not_the_one_stored(): void
    {
        /* Arrange: the row already moved from 2026-03-15 to 2026-04-15 */
        $id    = $this->seedRecurring('2026-03-15', '1M');
        $model = $this->model();
        self::assertTrue($model->claim_for_processing($id, '2026-03-15', '1M'));

        /* Act: a claimant that read an even older date */
        $stale = $model->claim_for_processing($id, '2026-02-15', '1M');

        /* Assert: the date condition, not just the id, gates the update */
        self::assertFalse($stale);
        $this->assertDatabaseHas('ip_invoices_recurring', ['invoice_recurring_id' => $id, 'recur_next_date' => '2026-04-15']);
    }

    #[Test]
    public function it_advances_only_the_claimed_row_when_two_rows_share_the_same_due_date(): void
    {
        /* Arrange */
        $first  = $this->seedRecurring('2026-03-15', '1M');
        $second = $this->seedRecurring('2026-03-15', '1M');

        /* Act */
        $claimed = $this->model()->claim_for_processing($first, '2026-03-15', '1M');

        /* Assert: the neighbour with the same date is untouched */
        self::assertTrue($claimed);
        $this->assertDatabaseHas('ip_invoices_recurring', ['invoice_recurring_id' => $first, 'recur_next_date' => '2026-04-15']);
        $this->assertDatabaseHas('ip_invoices_recurring', ['invoice_recurring_id' => $second, 'recur_next_date' => '2026-03-15']);
    }

    private function model(): object
    {
        $ci = $this->bootCodeIgniter();
        $ci->load->helper('date');
        $ci->load->model('invoices/mdl_invoices_recurring');

        return $ci->mdl_invoices_recurring;
    }

    private function seedRecurring(string $nextDate, string $frequency): int
    {
        $invoiceId = $this->seedInvoice($this->seedClient());

        return $this->databaseInsert('ip_invoices_recurring', [
            'invoice_id'       => $invoiceId,
            'recur_start_date' => '2026-01-15',
            'recur_end_date'   => '2030-01-15',
            'recur_frequency'  => $frequency,
            'recur_next_date'  => $nextDate,
        ]);
    }
}
