<?php

namespace Tests\Feature\Models;

use Mdl_Reports;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\UsesCodeIgniterModels;
use Tests\Support\FakeCiSettings;

/**
 * The figures behind the PDF reports. Each expectation is computed by hand from the seed data.
 */
#[CoversClass(Mdl_Reports::class)]
final class ReportsModelTest extends AbstractTestCase
{
    use UsesCodeIgniterModels;

    private Mdl_Reports $reports;

    protected function setUp(): void
    {
        parent::setUp();
        $ci                                     = $this->bootCodeIgniter();
        $ci->mdl_settings                       = new FakeCiSettings();
        $ci->mdl_settings->_data['date_format'] = 'Y-m-d';
        require_once APPPATH . 'helpers/date_helper.php';
        $this->reports = $this->ciModel('reports/models/Mdl_reports');
    }

    protected function tearDown(): void
    {
        $this->tearDownCodeIgniter();
        parent::tearDown();
    }

    // -- sales by client -----------------------------------------------------

    #[Test]
    public function it_totals_each_clients_sales_with_and_without_tax(): void
    {
        /* Arrange: Alpha 2 invoices (100/121 + 50/50), Beta 1 invoice (200/242), Gamma none */
        $alpha = $this->seedClient(['client_name' => 'Alpha', 'client_surname' => 'Org']);
        $beta  = $this->seedClient(['client_name' => 'Beta', 'client_surname' => 'Ltd']);
        $this->seedClient(['client_name' => 'Gamma', 'client_surname' => 'Idle']);
        $this->seedInvoice($alpha, [], ['invoice_item_subtotal' => '100.00', 'invoice_total' => '121.00']);
        $this->seedInvoice($alpha, [], ['invoice_item_subtotal' => '50.00', 'invoice_total' => '50.00']);
        $this->seedInvoice($beta, [], ['invoice_item_subtotal' => '200.00', 'invoice_total' => '242.00']);

        /* Act */
        $rows = $this->reports->sales_by_client();

        /* Assert */
        self::assertSame(['Alpha Org', 'Beta Ltd'], array_column($rows, 'client_namesurname'), 'Only invoiced clients, ordered by name.');
        self::assertSame(2, (int) $rows[0]->invoice_count);
        self::assertEqualsWithDelta(150.00, (float) $rows[0]->sales, 0.001);
        self::assertEqualsWithDelta(171.00, (float) $rows[0]->sales_with_tax, 0.001);
        self::assertSame(1, (int) $rows[1]->invoice_count);
        self::assertEqualsWithDelta(200.00, (float) $rows[1]->sales, 0.001);
        self::assertEqualsWithDelta(242.00, (float) $rows[1]->sales_with_tax, 0.001);
    }

    #[Test]
    public function it_restricts_sales_by_client_to_the_requested_period_inclusively(): void
    {
        /* Arrange */
        $alpha = $this->seedClient(['client_name' => 'Alpha', 'client_surname' => 'Org']);
        $beta  = $this->seedClient(['client_name' => 'Beta', 'client_surname' => 'Ltd']);
        $this->seedInvoice($alpha, ['invoice_date_created' => '2026-01-01'], ['invoice_item_subtotal' => '10.00', 'invoice_total' => '10.00']);
        $this->seedInvoice($alpha, ['invoice_date_created' => '2026-03-31'], ['invoice_item_subtotal' => '20.00', 'invoice_total' => '20.00']);
        $this->seedInvoice($alpha, ['invoice_date_created' => '2026-04-01'], ['invoice_item_subtotal' => '400.00', 'invoice_total' => '400.00']);
        $this->seedInvoice($beta, ['invoice_date_created' => '2025-12-31'], ['invoice_item_subtotal' => '999.00', 'invoice_total' => '999.00']);

        /* Act */
        $rows = $this->reports->sales_by_client('2026-01-01', '2026-03-31');

        /* Assert: both boundary days are in, the day after is out, and a client with nothing in range disappears */
        self::assertCount(1, $rows);
        self::assertSame(2, (int) $rows[0]->invoice_count);
        self::assertEqualsWithDelta(30.00, (float) $rows[0]->sales, 0.001);
        self::assertEqualsWithDelta(30.00, (float) $rows[0]->sales_with_tax, 0.001, 'The with-tax column has its own date filter.');
    }

    // -- invoices per client -------------------------------------------------

    #[Test]
    public function it_lists_invoices_per_client_within_the_period_only(): void
    {
        /* Arrange */
        $client = $this->seedClient(['client_name' => 'Per Client']);
        $this->seedInvoice($client, ['invoice_number' => 'IN-RANGE', 'invoice_date_created' => '2026-02-15'], ['invoice_total' => '75.00']);
        $this->seedInvoice($client, ['invoice_number' => 'TOO-EARLY', 'invoice_date_created' => '2025-12-31']);
        $this->seedInvoice($client, ['invoice_number' => 'TOO-LATE', 'invoice_date_created' => '2026-04-01']);

        /* Act */
        $rows = $this->reports->invoices_per_client('2026-01-01', '2026-03-31');

        /* Assert */
        self::assertSame(['IN-RANGE'], array_column($rows, 'invoice_number'));
        self::assertEqualsWithDelta(75.00, (float) $rows[0]->invoice_total, 0.001);
    }

    // -- sales by year -------------------------------------------------------

    #[Test]
    public function it_breaks_yearly_sales_down_by_quarter_per_client(): void
    {
        /* Arrange: A bills in Q1 and Q2, B in Q3; an invoice outside the period must not count */
        [$a, $b] = $this->yearlyClients();
        $this->seedInvoice($a, ['invoice_date_created' => '2025-11-30'], ['invoice_item_subtotal' => '9999', 'invoice_total' => '9999']);

        /* Act */
        $rows = $this->byName($this->reports->sales_by_year('2026-01-01', '2026-12-31', null, null, false));

        /* Assert */
        self::assertEqualsWithDelta(150.00, (float) $rows['A']->total_payment, 0.001);
        self::assertEqualsWithDelta(100.00, (float) $rows['A']->payment_t1_2026, 0.001);
        self::assertEqualsWithDelta(50.00, (float) $rows['A']->payment_t2_2026, 0.001);
        self::assertNull($rows['A']->payment_t3_2026);
        self::assertEqualsWithDelta(1000.00, (float) $rows['B']->total_payment, 0.001);
        self::assertEqualsWithDelta(1000.00, (float) $rows['B']->payment_t3_2026, 0.001);
        self::assertSame('VA', $rows['A']->VAT_ID);
    }

    #[Test]
    public function it_breaks_sales_down_by_quarter_in_the_quantity_band_query_too(): void
    {
        /* Arrange: the band query is a separate (duplicated) SQL path with its own quarter columns */
        $this->yearlyClients();

        /* Act */
        $rows = $this->byName($this->reports->sales_by_year('2026-01-01', '2026-12-31', 0, 100000, false));

        /* Assert */
        self::assertEqualsWithDelta(100.00, (float) $rows['A']->payment_t1_2026, 0.001);
        self::assertEqualsWithDelta(50.00, (float) $rows['A']->payment_t2_2026, 0.001);
        self::assertEqualsWithDelta(1000.00, (float) $rows['B']->payment_t3_2026, 0.001);
        self::assertNull($rows['A']->payment_t4_2026);
    }

    #[Test]
    public function it_reports_tax_inclusive_yearly_sales_when_asked(): void
    {
        /* Arrange */
        $this->yearlyClients();

        /* Act */
        $rows = $this->byName($this->reports->sales_by_year('2026-01-01', '2026-12-31', null, null, true));

        /* Assert: 121 + 60 and 1210 instead of the 150 / 1000 subtotals */
        self::assertEqualsWithDelta(181.00, (float) $rows['A']->total_payment, 0.001);
        self::assertEqualsWithDelta(1210.00, (float) $rows['B']->total_payment, 0.001);
    }

    #[Test]
    public function it_keeps_only_clients_whose_period_total_is_within_the_quantity_band(): void
    {
        /* Arrange */
        $this->yearlyClients();

        /* Act */
        $inBand   = $this->byName($this->reports->sales_by_year('2026-01-01', '2026-12-31', 200, 2000, false));
        $tooSmall = $this->byName($this->reports->sales_by_year('2026-01-01', '2026-12-31', 1, 100, false));

        /* Assert: A (150) is under the 200 floor; B (1000) is over the 100 ceiling */
        self::assertSame(['B'], array_keys($inBand));
        self::assertSame([], array_keys($tooSmall));
    }

    #[Test]
    public function it_adds_columns_for_every_year_in_a_multi_year_period(): void
    {
        /* Arrange */
        [$a] = $this->yearlyClients();
        $this->seedInvoice($a, ['invoice_date_created' => '2025-11-30'], ['invoice_item_subtotal' => '25', 'invoice_total' => '25']);

        /* Act */
        $rows = $this->byName($this->reports->sales_by_year('2025-01-01', '2026-12-31', null, null, false));

        /* Assert */
        self::assertEqualsWithDelta(25.00, (float) $rows['A']->payment_t4_2025, 0.001);
        self::assertEqualsWithDelta(100.00, (float) $rows['A']->payment_t1_2026, 0.001);
        self::assertEqualsWithDelta(175.00, (float) $rows['A']->total_payment, 0.001);
    }

    // -- aging ---------------------------------------------------------------

    #[Test]
    public function it_puts_every_overdue_balance_into_exactly_one_aging_bucket(): void
    {
        /* Arrange: one invoice per day offset, each with a distinct power-of-two balance so a
         * missing or double-counted invoice changes the sums unambiguously */
        $client  = $this->seedClient(['client_name' => 'Aging Client']);
        $offsets = [1 => 1, 15 => 2, 16 => 4, 30 => 8, 31 => 16, 90 => 32];
        foreach ($offsets as $daysAgo => $balance) {
            $this->seedInvoice($client, ['invoice_date_due' => date('Y-m-d', strtotime("-{$daysAgo} days"))], ['invoice_balance' => (string) $balance]);
        }
        $this->seedInvoice($client, ['invoice_date_due' => date('Y-m-d')], ['invoice_balance' => '1000']);
        $this->seedInvoice($client, ['invoice_date_due' => date('Y-m-d', strtotime('+5 days'))], ['invoice_balance' => '2000']);

        /* Act */
        $rows = $this->reports->invoice_aging();

        /* Assert: due today / future are not overdue; the three buckets partition the overdue total */
        self::assertCount(1, $rows);
        $row = $rows[0];
        self::assertEqualsWithDelta(1 + 2 + 4 + 8 + 16 + 32, (float) $row->total_balance, 0.001, 'total overdue balance');
        self::assertEqualsWithDelta(1 + 2, (float) $row->range_1, 0.001, '1-15 days overdue');
        self::assertEqualsWithDelta(4 + 8, (float) $row->range_2, 0.001, '16-30 days overdue');
        self::assertEqualsWithDelta(16 + 32, (float) $row->range_3, 0.001, '31+ days overdue');
        self::assertEqualsWithDelta(
            (float) $row->total_balance,
            (float) $row->range_1 + (float) $row->range_2 + (float) $row->range_3,
            0.001,
            'The aging buckets must add up to the total overdue balance.'
        );
    }

    #[Test]
    public function it_leaves_clients_without_overdue_balances_out_of_the_aging_report(): void
    {
        /* Arrange */
        $paid    = $this->seedClient(['client_name' => 'Paid Up']);
        $current = $this->seedClient(['client_name' => 'Not Due Yet']);
        $late    = $this->seedClient(['client_name' => 'Late Payer']);
        $this->seedInvoice($paid, ['invoice_date_due' => date('Y-m-d', strtotime('-40 days'))], ['invoice_balance' => '0.00']);
        $this->seedInvoice($current, ['invoice_date_due' => date('Y-m-d', strtotime('+10 days'))], ['invoice_balance' => '500.00']);
        $this->seedInvoice($late, ['invoice_date_due' => date('Y-m-d', strtotime('-3 days'))], ['invoice_balance' => '60.00']);

        /* Act */
        $rows = $this->reports->invoice_aging();

        /* Assert */
        self::assertSame(['Late Payer'], array_column($rows, 'client_name'));
    }

    /** @return array{int, int} */
    private function yearlyClients(): array
    {
        $a = $this->seedClient(['client_name' => 'A', 'client_surname' => 'One', 'client_vat_id' => 'VA']);
        $b = $this->seedClient(['client_name' => 'B', 'client_surname' => 'Two', 'client_vat_id' => 'VB']);
        $this->seedInvoice($a, ['invoice_date_created' => '2026-02-10'], ['invoice_item_subtotal' => '100', 'invoice_total' => '121']);
        $this->seedInvoice($a, ['invoice_date_created' => '2026-05-20'], ['invoice_item_subtotal' => '50', 'invoice_total' => '60']);
        $this->seedInvoice($b, ['invoice_date_created' => '2026-08-01'], ['invoice_item_subtotal' => '1000', 'invoice_total' => '1210']);

        return [$a, $b];
    }

    /**
     * @param list<object> $rows
     *
     * @return array<string, object>
     */
    private function byName(array $rows): array
    {
        return array_column(array_map(static fn (object $r): array => [$r->client_name, $r], $rows), 1, 0);
    }
}
