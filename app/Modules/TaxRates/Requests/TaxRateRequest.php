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

namespace FI\Modules\TaxRates\Requests;

use FI\Support\NumberFormatter;
use Illuminate\Foundation\Http\FormRequest;

class TaxRateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'name'    => trans('fi.name'),
            'percent' => trans('fi.percent'),
        ];
    }

    public function prepareForValidation()
    {
        $request = $this->all();

        $request['percent'] = NumberFormatter::unformat($request['percent']);

        $this->replace($request);
    }

    public function rules()
    {
        return [
            'name'    => 'required',
            'percent' => 'required|numeric',
        ];
    }
}