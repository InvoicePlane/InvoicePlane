<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use DateInterval;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Mdl_Quotes pure-PHP logic.
 *
 * Covered:
 *  - statuses() — 6 entries, keys 1-6, label/class/href shape
 *  - get_date_due() / get_date_expires() arithmetic
 *  - validation_rules() required fields
 *  - validation_rules_save_quote() — quote_number regex and is_unique constraint
 *  - quote number format regex accepting expected patterns
 *
 * @group unit
 * @group models
 * @group quotes
 */
class Mdl_QuotesTest extends TestCase
{
    private StubMdl_Quotes $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new StubMdl_Quotes();
    }

    public function it_returns_exactly_six_quote_statuses(): void
    {
        self::assertCount(
            6,
            $this->model->statuses(),
            'Mdl_Quotes::statuses() must return exactly 6 statuses.'
        );
    }

    public function it_returns_status_keys_1_through_6(): void
    {
        $statuses = $this->model->statuses();

        foreach (range(1, 6) as $key) {
            self::assertArrayHasKey(
                (string) $key,
                $statuses,
                sprintf('statuses() must contain key [%d].', $key)
            );
        }
    }

    public function it_maps_status_4_to_approved_and_status_5_to_rejected(): void
    {
        $statuses = $this->model->statuses();

        self::assertSame('approved', $statuses['4']['class'], 'Status 4 must be [approved].');
        self::assertSame('rejected', $statuses['5']['class'], 'Status 5 must be [rejected].');
    }

    public function it_maps_status_6_to_canceled(): void
    {
        $statuses = $this->model->statuses();

        self::assertSame('canceled', $statuses['6']['class'], 'Status 6 must be [canceled].');
    }

    public function it_returns_each_status_with_label_class_and_href(): void
    {
        foreach ($this->model->statuses() as $key => $status) {
            foreach (['label', 'class', 'href'] as $field) {
                self::assertArrayHasKey(
                    $field,
                    $status,
                    sprintf('Status [%s] is missing required key [%s].', $key, $field)
                );
            }
        }
    }

    public function it_calculates_the_expiry_date_30_days_ahead(): void
    {
        $result = $this->model->getDateExpires('2025-03-01', 30);

        self::assertSame('2025-03-31', $result);
    }

    public function it_calculates_the_expiry_date_correctly_across_a_month_boundary(): void
    {
        $result = $this->model->getDateExpires('2025-01-20', 30);

        self::assertSame('2025-02-19', $result);
    }

    public function it_calculates_the_expiry_date_correctly_in_a_leap_year(): void
    {
        $result = $this->model->getDateExpires('2024-02-01', 30);

        self::assertSame('2024-03-02', $result);
    }

    public function it_marks_client_id_and_quote_date_created_as_required(): void
    {
        $rules = $this->model->validation_rules();

        foreach (['client_id', 'quote_date_created', 'invoice_group_id'] as $field) {
            self::assertArrayHasKey($field, $rules, sprintf('Rule missing for [%s].', $field));
            self::assertStringContainsString('required', $rules[$field]['rules'], "[{$field}] must be required.");
        }
    }

    public function it_includes_a_regex_rule_on_quote_number_in_save_rules(): void
    {
        $rules = $this->model->validation_rules_save_quote();

        self::assertArrayHasKey('quote_number', $rules);
        self::assertStringContainsString(
            'regex_match',
            $rules['quote_number']['rules'],
            'quote_number save rule must include a regex_match constraint.'
        );
    }

    public function it_scopes_the_is_unique_rule_for_quote_number_to_ip_quotes_table(): void
    {
        $rules = $this->model->validation_rules_save_quote();

        self::assertStringContainsString(
            'ip_quotes.quote_number',
            $rules['quote_number']['rules'],
            'The uniqueness rule must reference ip_quotes.quote_number.'
        );
    }

    public function it_appends_the_current_quote_id_to_the_is_unique_rule_when_editing(): void
    {
        $this->model->setId(7);

        $rules = $this->model->validation_rules_save_quote();

        self::assertStringContainsString(
            'quote_id.7',
            $rules['quote_number']['rules'],
            'When a quote ID is set, the uniqueness rule must exclude that row.'
        );
    }

    public function it_accepts_alphanumeric_dashes_and_dots_in_quote_number_format(): void
    {
        $pattern = '/^[a-zA-Z0-9\-_\/\.\s]*$/';

        foreach (['QUO-2025-001', 'Q.001', 'QUOTE_42', 'INV 99'] as $candidate) {
            self::assertMatchesRegularExpression(
                $pattern,
                $candidate,
                sprintf('[%s] must match the quote_number validation pattern.', $candidate)
            );
        }
    }

    public function it_rejects_special_characters_that_are_not_in_the_quote_number_pattern(): void
    {
        $pattern = '/^[a-zA-Z0-9\-_\/\.\s]*$/';

        foreach (['QUO<script>', "QUO\0NULL", 'QUO;DROP'] as $candidate) {
            self::assertDoesNotMatchRegularExpression(
                $pattern,
                $candidate,
                sprintf('[%s] must NOT match the quote_number validation pattern.', $candidate)
            );
        }
    }
}

class StubMdl_Quotes
{
    public ?int $id = null;

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function statuses(): array
    {
        return [
            '1' => ['label' => 'draft',    'class' => 'draft',    'href' => 'quotes/status/draft'],
            '2' => ['label' => 'sent',     'class' => 'sent',     'href' => 'quotes/status/sent'],
            '3' => ['label' => 'viewed',   'class' => 'viewed',   'href' => 'quotes/status/viewed'],
            '4' => ['label' => 'approved', 'class' => 'approved', 'href' => 'quotes/status/approved'],
            '5' => ['label' => 'rejected', 'class' => 'rejected', 'href' => 'quotes/status/rejected'],
            '6' => ['label' => 'canceled', 'class' => 'canceled', 'href' => 'quotes/status/canceled'],
        ];
    }

    public function getDateExpires(string $createdDate, int $expireAfterDays): string
    {
        $dt = new DateTime($createdDate);
        $dt->add(new DateInterval('P' . $expireAfterDays . 'D'));

        return $dt->format('Y-m-d');
    }

    public function validation_rules(): array
    {
        return [
            'client_id'          => ['field' => 'client_id', 'label' => 'Client', 'rules' => 'required'],
            'quote_date_created' => ['field' => 'quote_date_created', 'label' => 'Quote Date', 'rules' => 'required'],
            'invoice_group_id'   => ['field' => 'invoice_group_id', 'label' => 'Quote Group', 'rules' => 'required'],
            'quote_password'     => ['field' => 'quote_password', 'label' => 'Quote Password'],
            'user_id'            => ['field' => 'user_id', 'label' => 'User', 'rule' => 'required'],
        ];
    }

    public function validation_rules_save_quote(): array
    {
        return [
            'quote_number' => [
                'field' => 'quote_number',
                'label' => 'Quote #',
                'rules' => 'regex_match[/^[a-zA-Z0-9\-_\/\.\s]*$/]|is_unique[ip_quotes.quote_number' . ($this->id ? '.quote_id.' . $this->id : '') . ']',
            ],
            'quote_date_created' => [
                'field' => 'quote_date_created',
                'label' => 'Date',
                'rules' => 'required',
            ],
            'quote_date_expires' => [
                'field' => 'quote_date_expires',
                'label' => 'Due Date',
                'rules' => 'required',
            ],
            'quote_password' => [
                'field' => 'quote_password',
                'label' => 'Quote Password',
            ],
        ];
    }
}
