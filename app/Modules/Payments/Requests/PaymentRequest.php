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

namespace IP\Modules\Payments\Requests;

use IP\Support\NumberFormatter;
use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'paid_at' => trans('ip.payment_date'),
            'invoice_id' => trans('ip.invoice'),
            'amount' => trans('ip.amount'),
            'payment_method_id' => trans('ip.payment_method'),
        ];
    }

    public function prepareForValidation()
    {
        $request = $this->all();

        $request['amount'] = (isset($request['amount'])) ? NumberFormatter::unformat($request['amount']) : null;

        $this->replace($request);
    }

    public function rules()
    {
        return [
            'paid_at' => 'required',
            'invoice_id' => 'required',
            'amount' => 'required|numeric',
            'payment_method_id' => 'required',
        ];
    }
}