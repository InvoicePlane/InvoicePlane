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

namespace FI\Modules\Invoices\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Clients\Models\Client;
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Groups\Models\Group;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Invoices\Models\InvoiceItem;
use FI\Modules\Invoices\Requests\InvoiceStoreRequest;
use FI\Support\DateFormatter;

class InvoiceCopyController extends Controller
{
    public function create()
    {
        $invoice = Invoice::find(request('invoice_id'));

        return view('invoices._modal_copy')
            ->with('invoice', $invoice)
            ->with('groups', Group::getList())
            ->with('companyProfiles', CompanyProfile::getList())
            ->with('invoice_date', DateFormatter::format())
            ->with('user_id', auth()->user()->id);
    }

    public function store(InvoiceStoreRequest $request)
    {
        $client = Client::firstOrCreateByUniqueName($request->input('client_name'));

        $fromInvoice = Invoice::find($request->input('invoice_id'));

        $toInvoice = Invoice::create([
            'client_id' => $client->id,
            'company_profile_id' => $request->input('company_profile_id'),
            'invoice_date' => DateFormatter::unformat(request('invoice_date')),
            'group_id' => $request->input('group_id'),
            'currency_code' => $fromInvoice->currency_code,
            'exchange_rate' => $fromInvoice->exchange_rate,
            'terms' => $fromInvoice->terms,
            'footer' => $fromInvoice->footer,
            'template' => $fromInvoice->template,
            'summary' => $fromInvoice->summary,
            'discount' => $fromInvoice->discount,
        ]);

        foreach ($fromInvoice->items as $item) {
            InvoiceItem::create([
                'invoice_id' => $toInvoice->id,
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
        $custom = collect($fromInvoice->custom)->except('invoice_id')->toArray();
        $toInvoice->custom->update($custom);

        return response()->json(['id' => $toInvoice->id], 200);
    }
}