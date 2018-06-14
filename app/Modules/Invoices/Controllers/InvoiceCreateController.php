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

namespace IP\Modules\Invoices\Controllers;

use IP\Http\Controllers\Controller;
use IP\Modules\Clients\Models\Client;
use IP\Modules\CompanyProfiles\Models\CompanyProfile;
use IP\Modules\Groups\Models\Group;
use IP\Modules\Invoices\Models\Invoice;
use IP\Modules\Invoices\Requests\InvoiceStoreRequest;
use IP\Support\DateFormatter;

class InvoiceCreateController extends Controller
{
    public function create()
    {
        return view('invoices._modal_create')
            ->with('companyProfiles', CompanyProfile::getList())
            ->with('groups', Group::getList());
    }

    public function store(InvoiceStoreRequest $request)
    {
        $input = $request->except('client_name');

        $input['client_id'] = Client::firstOrCreateByUniqueName($request->input('client_name'))->id;
        $input['invoice_date'] = DateFormatter::unformat($input['invoice_date']);

        $invoice = Invoice::create($input);

        return response()->json(['id' => $invoice->id], 200);
    }
}