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
use FI\Modules\Invoices\Requests\InvoiceStoreRequest;
use FI\Support\DateFormatter;

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