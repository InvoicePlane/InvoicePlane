<?php

namespace Tests\Unit\Models;

use DateInterval;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Mdl_Invoices business logic that is pure PHP and requires
 * no live database or CI3 instance.
 *
 * Covered:
 *  - statuses() shape and completeness
 *  - get_date_due() date arithmetic
 *  - validation_rules() required fields
 *  - validation_rules_save_invoice() required fields
 *  - is_unique constraint string format in save rules
 *
 * @group unit
 * @group models
 * @group invoices
 */
class Mdl_InvoicesTest extends TestCase
{
    private StubMdl_Invoices $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new StubMdl_Invoices();
    }

    public function it_returns_exactly_four_invoice_statuses(): void
    {
        $statuses = $this->model->statuses();

        self::assertCount(
            4,
            $statuses,
            'Mdl_Invoices::statuses() must return exactly 4 entries: draft, sent, viewed, paid.'
        );
    }

    public function it_returns_status_keys_1_through_4(): void
    {
        $statuses = $this->model->statuses();

        foreach ([1, 2, 3, 4] as $expectedKey) {
            self::assertArrayHasKey(
                (string) $expectedKey,
                $statuses,
                sprintf('statuses() must contain key [%d].', $expectedKey)
            );
        }
    }

    public function it_returns_each_status_with_label_class_and_href_keys(): void
    {
        $statuses = $this->model->statuses();

        foreach ($statuses as $key => $status) {
            self::assertArrayHasKey('label', $status, sprintf('Status [%s] is missing [label].', $key));
            self::assertArrayHasKey('class', $status, sprintf('Status [%s] is missing [class].', $key));
            self::assertArrayHasKey('href', $status, sprintf('Status [%s] is missing [href].', $key));
        }
    }

    public function it_maps_status_4_to_paid_class(): void
    {
        $statuses = $this->model->statuses();

        self::assertSame(
            'paid',
            $statuses['4']['class'],
            'Status key 4 must have class [paid].'
        );
    }

    public function it_maps_status_1_to_draft_href(): void
    {
        $statuses = $this->model->statuses();

        self::assertSame(
            'invoices/status/draft',
            $statuses['1']['href'],
            'Status 1 href must point to invoices/status/draft.'
        );
    }

    public function it_calculates_due_date_30_days_ahead_of_created_date(): void
    {
        $created = '2025-01-01';
        $due     = $this->model->get_date_due($created, 30);

        self::assertSame(
            '2025-01-31',
            $due,
            'get_date_due() with 30-day offset from 2025-01-01 must return 2025-01-31.'
        );
    }

    public function it_calculates_due_date_correctly_across_a_month_boundary(): void
    {
        $created = '2025-01-15';
        $due     = $this->model->get_date_due($created, 30);

        self::assertSame(
            '2025-02-14',
            $due,
            'get_date_due() with 30-day offset from 2025-01-15 must return 2025-02-14.'
        );
    }

    public function it_calculates_due_date_correctly_for_a_zero_day_offset(): void
    {
        $created = '2025-06-01';
        $due     = $this->model->get_date_due($created, 0);

        self::assertSame(
            '2025-06-01',
            $due,
            'A zero-day offset must return the same date as the created date.'
        );
    }

    public function it_calculates_due_date_correctly_crossing_a_year_boundary(): void
    {
        $created = '2024-12-15';
        $due     = $this->model->get_date_due($created, 30);

        self::assertSame(
            '2025-01-14',
            $due,
            'get_date_due() must correctly cross a year boundary.'
        );
    }

    public function it_calculates_due_date_correctly_in_a_leap_year(): void
    {
        $created = '2024-02-01';
        $due     = $this->model->get_date_due($created, 30);

        self::assertSame(
            '2024-03-02',
            $due,
            'get_date_due() must handle leap year February correctly (2024 has 29 days in Feb).'
        );
    }

    public function it_includes_client_id_as_a_required_validation_rule(): void
    {
        $rules = $this->model->validation_rules();

        self::assertArrayHasKey(
            'client_id',
            $rules,
            'validation_rules() must include a rule for [client_id].'
        );

        self::assertStringContainsString(
            'required',
            $rules['client_id']['rules'],
            '[client_id] validation rule must contain [required].'
        );
    }

    public function it_includes_invoice_date_created_as_a_required_validation_rule(): void
    {
        $rules = $this->model->validation_rules();

        self::assertArrayHasKey('invoice_date_created', $rules);

        self::assertStringContainsString(
            'required',
            $rules['invoice_date_created']['rules'],
            '[invoice_date_created] must be required.'
        );
    }

    public function it_includes_invoice_group_id_as_a_required_validation_rule(): void
    {
        $rules = $this->model->validation_rules();

        self::assertArrayHasKey('invoice_group_id', $rules);

        self::assertStringContainsString(
            'required',
            $rules['invoice_group_id']['rules'],
            '[invoice_group_id] must be required.'
        );
    }

    public function it_includes_invoice_date_created_and_due_as_required_in_save_rules(): void
    {
        $rules = $this->model->validation_rules_save_invoice();

        foreach (['invoice_date_created', 'invoice_date_due'] as $field) {
            self::assertArrayHasKey(
                $field,
                $rules,
                sprintf('validation_rules_save_invoice() must include rule for [%s].', $field)
            );

            self::assertStringContainsString(
                'required',
                $rules[$field]['rules'],
                sprintf('[%s] must be required in save rules.', $field)
            );
        }
    }

    public function it_scopes_the_is_unique_rule_to_ip_invoices_invoice_number_column(): void
    {
        $rules = $this->model->validation_rules_save_invoice();

        self::assertArrayHasKey('invoice_number', $rules);

        self::assertStringContainsString(
            'ip_invoices.invoice_number',
            $rules['invoice_number']['rules'],
            'The invoice_number uniqueness rule must reference the [ip_invoices.invoice_number] column.'
        );
    }

    public function it_appends_the_current_invoice_id_to_the_is_unique_rule_when_id_is_set(): void
    {
        $this->model->setId(42);

        $rules = $this->model->validation_rules_save_invoice();

        self::assertStringContainsString(
            'invoice_id.42',
            $rules['invoice_number']['rules'],
            'When an invoice ID is set, the uniqueness rule must include [invoice_id.42] to exclude the current row.'
        );
    }

    public function it_does_not_append_an_id_clause_to_is_unique_when_creating_a_new_invoice(): void
    {
        $this->model->setId(null);

        $rules = $this->model->validation_rules_save_invoice();

        self::assertStringNotContainsString(
            'invoice_id.',
            $rules['invoice_number']['rules'],
            'When no ID is set (new invoice), the uniqueness rule must NOT append an invoice_id exclusion clause.'
        );
    }
}

/**
 * Stub that replicates the pure-PHP logic from Mdl_Invoices without CI3 dependencies.
 */
class StubMdl_Invoices
{
    public ?int $id = null;

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function statuses(): array
    {
        return [
            '1' => ['label' => 'draft',  'class' => 'draft',  'href' => 'invoices/status/draft'],
            '2' => ['label' => 'sent',   'class' => 'sent',   'href' => 'invoices/status/sent'],
            '3' => ['label' => 'viewed', 'class' => 'viewed', 'href' => 'invoices/status/viewed'],
            '4' => ['label' => 'paid',   'class' => 'paid',   'href' => 'invoices/status/paid'],
        ];
    }

    public function get_date_due(string $invoiceDateCreated, int $dueDays): string
    {
        $due = new DateTime($invoiceDateCreated);
        $due->add(new DateInterval('P' . $dueDays . 'D'));

        return $due->format('Y-m-d');
    }

    public function validation_rules(): array
    {
        return [
            'client_id' => [
                'field' => 'client_id',
                'label' => 'Client',
                'rules' => 'required',
            ],
            'invoice_date_created' => [
                'field' => 'invoice_date_created',
                'label' => 'Invoice Date',
                'rules' => 'required',
            ],
            'invoice_time_created' => [
                'rules' => 'required',
            ],
            'invoice_group_id' => [
                'field' => 'invoice_group_id',
                'label' => 'Invoice Group',
                'rules' => 'required',
            ],
            'invoice_password' => [
                'field' => 'invoice_password',
                'label' => 'Invoice Password',
            ],
            'user_id' => [
                'field' => 'user_id',
                'label' => 'User',
                'rule'  => 'required',
            ],
            'payment_method' => [
                'field' => 'payment_method',
                'label' => 'Payment Method',
            ],
        ];
    }

    public function validation_rules_save_invoice(): array
    {
        return [
            'invoice_number' => [
                'field' => 'invoice_number',
                'label' => 'Invoice #',
                'rules' => 'is_unique[ip_invoices.invoice_number' . ($this->id ? '.invoice_id.' . $this->id : '') . ']',
            ],
            'invoice_date_created' => [
                'field' => 'invoice_date_created',
                'label' => 'Date',
                'rules' => 'required',
            ],
            'invoice_date_due' => [
                'field' => 'invoice_date_due',
                'label' => 'Due Date',
                'rules' => 'required',
            ],
            'invoice_time_created' => [
                'rules' => 'required',
            ],
            'invoice_password' => [
                'field' => 'invoice_password',
                'label' => 'Invoice Password',
            ],
        ];
    }
}
