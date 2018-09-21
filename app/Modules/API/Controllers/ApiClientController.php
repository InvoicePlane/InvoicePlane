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

use Illuminate\Http\Request;
use IP\Modules\API\Requests\APIClientUpdateRequest;
use IP\Modules\Clients\Models\Client;
use IP\Modules\Clients\Requests\ClientStoreRequest;

class ApiClientController extends ApiController
{
    public function buildFailedValidationResponse(Request $request, array $errors)
    {
        return response()->json($errors, 422);
    }

    public function lists()
    {
        $clients = Client::getSelect()
            ->orderBy('name')
            ->paginate(config('ip.resultsPerPage'));

        return response()->json($clients);
    }

    public function show()
    {
        if ($client = Client::getSelect()->find(request('id'))) {
            return response()->json($client);
        }

        return response()->json([trans('ip.record_not_found')], 400);

    }

    public function store(ClientStoreRequest $request)
    {
        return response()->json(Client::create($request->except('key', 'signature', 'timestamp', 'endpoint')));
    }

    public function update(APIClientUpdateRequest $request)
    {
        if ($client = Client::getSelect()->find($request->input('id'))) {
            $client->fill($request->except('key', 'signature', 'timestamp', 'endpoint'));

            $client->save();

            return response()->json($client);
        }

        return response()->json([trans('ip.record_not_found')], 400);

    }

    public function delete()
    {
        $validator = $this->validator->make(request()->only(['id']), ['id' => 'required']);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }

        if ($client = Client::getSelect()->find(request('id'))) {
            Client::destroy(request('id'));

            return response(200);
        }

        return response()->json([trans('ip.record_not_found')], 400);
    }
}
