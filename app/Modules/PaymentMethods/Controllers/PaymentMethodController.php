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

namespace IP\Modules\PaymentMethods\Controllers;

use IP\Http\Controllers\Controller;
use IP\Modules\PaymentMethods\Models\PaymentMethod;
use IP\Modules\PaymentMethods\Requests\PaymentMethodRequest;
use IP\Traits\ReturnUrl;

class PaymentMethodController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        $paymentMethods = PaymentMethod::sortable(['name' => 'asc'])->paginate(config('ip.resultsPerPage'));

        return view('payment_methods.index')
            ->with('paymentMethods', $paymentMethods);
    }

    public function create()
    {
        return view('payment_methods.form')
            ->with('editMode', false);
    }

    public function store(PaymentMethodRequest $request)
    {
        PaymentMethod::create($request->all());

        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('ip.record_successfully_created'));
    }

    public function edit($id)
    {
        $paymentMethod = PaymentMethod::find($id);

        return view('payment_methods.form')
            ->with(['editMode' => true, 'paymentMethod' => $paymentMethod]);
    }

    public function update(PaymentMethodRequest $request, $id)
    {
        $paymentMethod = PaymentMethod::find($id);

        $paymentMethod->fill($request->all());

        $paymentMethod->save();

        return redirect($this->getReturnUrl())
            ->with('alertInfo', trans('ip.record_successfully_updated'));
    }

    public function delete($id)
    {
        PaymentMethod::destroy($id);

        return redirect()->route('paymentMethods.index')
            ->with('alert', trans('ip.record_successfully_deleted'));
    }
}