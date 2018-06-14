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

namespace IP\Modules\RecurringInvoices\Controllers;

use IP\Http\Controllers\Controller;
use IP\Modules\Clients\Models\Client;
use IP\Modules\CompanyProfiles\Models\CompanyProfile;
use IP\Modules\Groups\Models\Group;
use IP\Modules\RecurringInvoices\Models\RecurringInvoice;
use IP\Modules\RecurringInvoices\Models\RecurringInvoiceItem;
use IP\Modules\RecurringInvoices\Requests\RecurringInvoiceStoreRequest;
use IP\Support\DateFormatter;
use IP\Support\Frequency;

class RecurringInvoiceCopyController extends Controller
{
    public function create()
    {
        return view('recurring_invoices._modal_copy')
            ->with('recurringInvoice', RecurringInvoice::find(request('recurring_invoice_id')))
            ->with('groups', Group::getList())
            ->with('companyProfiles', CompanyProfile::getList())
            ->with('frequencies', Frequency::lists());
    }

    public function store(RecurringInvoiceStoreRequest $request)
    {
        $client = Client::firstOrCreateByUniqueName($request->input('client_name'));

        $fromRecurringInvoice = RecurringInvoice::find($request->input('recurring_invoice_id'));

        $toRecurringInvoice = RecurringInvoice::create([
            'client_id' => $client->id,
            'company_profile_id' => $request->input('company_profile_id'),
            'group_id' => $request->input('group_id'),
            'currency_code' => $fromRecurringInvoice->currency_code,
            'exchange_rate' => $fromRecurringInvoice->exchange_rate,
            'terms' => $fromRecurringInvoice->terms,
            'footer' => $fromRecurringInvoice->footer,
            'template' => $fromRecurringInvoice->template,
            'summary' => $fromRecurringInvoice->summary,
            'discount' => $fromRecurringInvoice->discount,
            'recurring_frequency' => $request->input('recurring_frequency'),
            'recurring_period' => $request->input('recurring_period'),
            'next_date' => DateFormatter::unformat($request->input('next_date')),
            'stop_date' => ($request->input('stop_date') ? DateFormatter::unformat($request->input('stop_date')) : '0000-00-00'),
        ]);

        foreach ($fromRecurringInvoice->items as $item) {
            RecurringInvoiceItem::create([
                'recurring_invoice_id' => $toRecurringInvoice->id,
                'name' => $item->name,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'tax_rate_id' => $item->tax_rate_id,
                'tax_rate_2_id' => $item->tax_rate_2_id,
                'display_order' => $item->display_order,
            ]);
        }

        // Copy the custom fields
        $custom = collect($fromRecurringInvoice->custom)->except('recurring_invoice_id')->toArray();
        $toRecurringInvoice->custom->update($custom);

        return response()->json(['success' => true, 'url' => route('recurringInvoices.edit', [$toRecurringInvoice->id])], 200);
    }
}