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

namespace FI\Modules\Payments\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\CustomFields\Models\CustomField;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\PaymentMethods\Models\PaymentMethod;
use FI\Modules\Payments\Models\Payment;
use FI\Modules\Payments\Requests\PaymentRequest;
use FI\Support\DateFormatter;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::select('payments.*')
            ->with(['invoice.client', 'invoice.currency', 'paymentMethod'])
            ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->join('clients', 'clients.id', '=', 'invoices.client_id')
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'payments.payment_method_id')
            ->keywords(request('search'))
            ->clientId(request('client'))
            ->sortable(['paid_at' => 'desc', 'length(number)' => 'desc', 'number' => 'desc'])
            ->paginate(config('fi.resultsPerPage'));

        return view('payments.index')
            ->with('payments', $payments)
            ->with('displaySearch', true);
    }

    public function create()
    {
        $date = DateFormatter::format();

        $invoice = Invoice::find(request('invoice_id'));

        return view('payments._modal_enter_payment')
            ->with('invoice_id', request('invoice_id'))
            ->with('invoiceNumber', $invoice->number)
            ->with('balance', $invoice->amount->formatted_numeric_balance)
            ->with('date', $date)
            ->with('paymentMethods', PaymentMethod::getList())
            ->with('client', $invoice->client)
            ->with('customFields', CustomField::forTable('payments')->get())
            ->with('redirectTo', request('redirectTo'));
    }

    public function store(PaymentRequest $request)
    {
        $input = $request->except('custom', 'email_payment_receipt');

        $input['paid_at'] = DateFormatter::unformat($input['paid_at']);

        $payment = Payment::create($input);

        $payment->custom->update($request->input('custom', []));

        return response()->json(['success' => true], 200);
    }

    public function edit($id)
    {
        $payment = Payment::find($id);

        return view('payments.form')
            ->with('editMode', true)
            ->with('payment', $payment)
            ->with('paymentMethods', PaymentMethod::getList())
            ->with('invoice', $payment->invoice)
            ->with('customFields', CustomField::forTable('payments')->get());
    }

    public function update(PaymentRequest $request, $id)
    {
        $input = $request->except('custom');

        $input['paid_at'] = DateFormatter::unformat($input['paid_at']);

        $payment = Payment::find($id);
        $payment->fill($input);
        $payment->save();

        $payment->custom->update($request->input('custom', []));

        return redirect()->route('payments.index')
            ->with('alertInfo', trans('fi.record_successfully_updated'));
    }

    public function delete($id)
    {
        Payment::destroy($id);

        return redirect()->route('payments.index')
            ->with('alert', trans('fi.record_successfully_deleted'));
    }

    public function bulkDelete()
    {
        Payment::destroy(request('ids'));
    }
}