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

namespace IP\Modules\ItemLookups\Requests;

use Illuminate\Foundation\Http\FormRequest;
use IP\Support\NumberFormatter;

class ItemLookupRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'name' => trans('ip.name'),
            'price' => trans('ip.price'),
        ];
    }

    public function prepareForValidation()
    {
        $request = $this->all();

        $request['price'] = NumberFormatter::unformat($request['price']);

        $this->replace($request);
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'price' => 'required|numeric',
        ];
    }
}
