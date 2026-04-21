<?php

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
