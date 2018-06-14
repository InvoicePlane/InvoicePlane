<?php

/**
 * InvoicePlane
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (C) 2014 - 2018 InvoicePlane
 * @license     https://invoiceplane.com/license
 * @link        https://invoiceplane.com
 *
 * Based on FusionInvoice by Jesse Terry (FusionInvoice, LLC)
 */

namespace IP\Modules\Exports\Support\Results;

use IP\Modules\Invoices\Models\Invoice;

class Invoices implements SourceInterface
{
    public function getResults($params = [])
    {
        $invoice = Invoice::select('invoices.number', 'invoices.created_at', 'invoices.updated_at', 'invoices.invoice_date',
            'invoices.due_at', 'invoices.terms', 'invoices.footer', 'invoices.url_key', 'invoices.currency_code',
            'invoices.exchange_rate', 'invoices.template', 'invoices.summary', 'groups.name AS group', 'clients.name AS client_name',
            'clients.email AS client_email', 'clients.address AS client_address', 'clients.city AS client_city',
            'clients.state AS client_state', 'clients.zip AS client_zip', 'clients.country AS client_country',
            'users.name AS user_name', 'users.email AS user_email',
            'company_profiles.company AS company', 'company_profiles.address AS company_address',
            'company_profiles.city AS company_city', 'company_profiles.state AS company_state',
            'company_profiles.zip AS company_zip', 'company_profiles.country AS company_country',
            'invoice_amounts.subtotal', 'invoice_amounts.tax', 'invoice_amounts.total',
            'invoice_amounts.paid', 'invoice_amounts.balance')
            ->join('invoice_amounts', 'invoice_amounts.invoice_id', '=', 'invoices.id')
            ->join('clients', 'clients.id', '=', 'invoices.client_id')
            ->join('groups', 'groups.id', '=', 'invoices.group_id')
            ->join('users', 'users.id', '=', 'invoices.user_id')
            ->join('company_profiles', 'company_profiles.id', '=', 'invoices.company_profile_id')
            ->orderBy('number');

        return $invoice->get()->toArray();
    }
}