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

namespace IP\Modules\API\Controllers;

use IP\Modules\API\Requests\APIInvoiceItemRequest;
use IP\Modules\API\Requests\APIInvoiceStoreRequest;
use IP\Modules\Clients\Models\Client;
use IP\Modules\Invoices\Models\Invoice;
use IP\Modules\Invoices\Models\InvoiceItem;
use IP\Modules\Users\Models\User;

class ApiInvoiceController extends ApiController
{
    public function lists()
    {
        $invoices = Invoice::select('invoices.*')
            ->with(['items.amount', 'client', 'amount', 'currency'])
            ->status(request('status'))
            ->sortable(['invoice_date' => 'desc', 'LENGTH(number)' => 'desc', 'number' => 'desc'])
            ->paginate(config('fi.resultsPerPage'));

        return response()->json($invoices);
    }

    public function show()
    {
        return response()->json(Invoice::with(['items.amount', 'client', 'amount', 'currency'])->find(request('id')));
    }

    public function store(APIInvoiceStoreRequest $request)
    {
        $input = $request->except('key', 'signature', 'timestamp', 'endpoint');

        $input['user_id'] = User::where('client_id', 0)->where('api_public_key', $request->input('key'))->first()->id;

        $input['client_id'] = Client::firstOrCreateByUniqueName($request->input('client_name'))->id;

        unset($input['client_name']);

        return response()->json(Invoice::create($input));
    }

    public function addItem(APIInvoiceItemRequest $request)
    {
        $input = $request->except('key', 'signature', 'timestamp', 'endpoint');

        InvoiceItem::create($input);
    }

    public function delete()
    {
        $validator = $this->validator->make(['id' => request('id')], ['id' => 'required']);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }

        if (Invoice::find(request('id'))) {
            Invoice::destroy(request('id'));

            return response(200);
        }

        return response()->json([trans('ip.record_not_found')], 400);
    }
}