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

namespace IP\Modules\API\Requests;

use IP\Support\NumberFormatter;
use Illuminate\Foundation\Http\FormRequest;

class APIInvoiceItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'name' => trans('ip.name'),
            'quantity' => trans('ip.quantity'),
            'price' => trans('ip.price'),
        ];
    }

    public function prepareForValidation()
    {
        $request = $this->all();

        $request['quantity'] = NumberFormatter::unformat($request['quantity']);
        $request['price'] = NumberFormatter::unformat($request['price']);

        $this->replace($request);
    }

    public function rules()
    {
        return [
            'invoice_id' => 'required',
            'name' => 'required_with:price,quantity',
            'quantity' => 'required_with:price,name|numeric',
            'price' => 'required_with:name,quantity|numeric',
        ];
    }
}