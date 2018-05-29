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

namespace FI\Modules\Clients\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Clients\Models\Client;
use FI\Modules\Clients\Requests\ClientStoreRequest;
use FI\Modules\Clients\Requests\ClientUpdateRequest;
use FI\Modules\CustomFields\Models\CustomField;
use FI\Modules\Payments\Models\Payment;
use FI\Support\Frequency;
use FI\Traits\ReturnUrl;

class ClientController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        $status = (request('status')) ?: 'all';

        $clients = Client::getSelect()
            ->leftJoin('clients_custom', 'clients_custom.client_id', '=', 'clients.id')
            ->with(['currency'])
            ->sortable(['name' => 'asc'])
            ->status($status)
            ->keywords(request('search'))
            ->paginate(config('fi.resultsPerPage'));

        return view('clients.index')
            ->with('clients', $clients)
            ->with('status', $status)
            ->with('displaySearch', true);
    }

    public function create()
    {
        return view('clients.form')
            ->with('editMode', false)
            ->with('customFields', CustomField::forTable('clients')->get());
    }

    public function store(ClientStoreRequest $request)
    {
        $client = Client::create($request->except('custom'));

        $client->custom->update($request->get('custom', []));

        return redirect()->route('clients.edit', [$client->id])
            ->with('alertInfo', trans('fi.record_successfully_created'));
    }

    public function show($clientId)
    {
        $this->setReturnUrl();

        $client = Client::getSelect()->find($clientId);

        $invoices = $client->invoices()
            ->with(['client', 'activities', 'amount.invoice.currency'])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->take(config('fi.resultsPerPage'))->get();

        $quotes = $client->quotes()
            ->with(['client', 'activities', 'amount.quote.currency'])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->take(config('fi.resultsPerPage'))->get();

        $recurringInvoices = $client->recurringInvoices()
            ->with(['client', 'amount.recurringInvoice.currency'])
            ->orderBy('next_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(config('fi.resultsPerPage'))->get();

        return view('clients.view')
            ->with('client', $client)
            ->with('invoices', $invoices)
            ->with('quotes', $quotes)
            ->with('payments', Payment::clientId($clientId)->orderBy('paid_at', 'desc')->get())
            ->with('recurringInvoices', $recurringInvoices)
            ->with('customFields', CustomField::forTable('clients')->get())
            ->with('frequencies', Frequency::lists());
    }

    public function edit($clientId)
    {
        $client = Client::getSelect()->with(['custom'])->find($clientId);

        return view('clients.form')
            ->with('editMode', true)
            ->with('client', $client)
            ->with('customFields', CustomField::forTable('clients')->get())
            ->with('returnUrl', $this->getReturnUrl());
    }

    public function update(ClientUpdateRequest $request, $id)
    {
        $client = Client::find($id);
        $client->fill($request->except('custom'));
        $client->save();

        $client->custom->update($request->get('custom', []));

        return redirect()->route('clients.edit', [$id])
            ->with('alertInfo', trans('fi.record_successfully_updated'));
    }

    public function delete($clientId)
    {
        Client::destroy($clientId);

        return redirect()->route('clients.index')
            ->with('alert', trans('fi.record_successfully_deleted'));
    }

    public function bulkDelete()
    {
        Client::destroy(request('ids'));
    }

    public function ajaxLookup()
    {
        $clients = Client::select('unique_name')
            ->where('active', 1)
            ->where('unique_name', 'like', '%' . request('query') . '%')
            ->orderBy('unique_name')
            ->get();

        $list = [];

        foreach ($clients as $client) {
            $list[]['value'] = $client->unique_name;
        }

        return json_encode($list);
    }

    public function ajaxModalEdit()
    {
        return view('clients._modal_edit')
            ->with('editMode', true)
            ->with('client', Client::getSelect()->with(['custom'])->find(request('client_id')))
            ->with('refreshToRoute', request('refresh_to_route'))
            ->with('id', request('id'))
            ->with('customFields', CustomField::forTable('clients')->get());
    }

    public function ajaxModalUpdate(ClientUpdateRequest $request, $id)
    {
        $client = Client::find($id);
        $client->fill($request->except('custom'));
        $client->save();

        $client->custom->update($request->get('custom', []));

        return response()->json(['success' => true], 200);
    }

    public function ajaxModalLookup()
    {
        return view('clients._modal_lookup')
            ->with('updateClientIdRoute', request('update_client_id_route'))
            ->with('refreshToRoute', request('refresh_to_route'))
            ->with('id', request('id'));
    }

    public function ajaxCheckName()
    {
        $client = Client::select('id')->where('unique_name', request('client_name'))->first();

        if ($client) {
            return response()->json(['success' => true, 'client_id' => $client->id], 200);
        }

        return response()->json([
            'success' => false,
            'errors' => ['messages' => [trans('fi.client_not_found')]],
        ], 400);
    }

    public function ajaxCheckDuplicateName()
    {
        if (Client::where(function ($query) {
                $query->where('name', request('client_name'));
                $query->orWhere('unique_name', request('unique_name'));
            })->where('id', '<>', request('client_id'))->count() > 0
        ) {
            return response()->json(['is_duplicate' => 1]);
        }

        return response()->json(['is_duplicate' => 0]);
    }
}