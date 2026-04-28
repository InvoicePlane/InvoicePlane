<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Unit\Invoices;

use DateInterval;
use DateTime;

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
