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

namespace IP\Modules\Groups\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'name' => trans('ip.name'),
            'next_id' => trans('ip.next_number'),
            'left_pad' => trans('ip.left_pad'),
            'format' => trans('ip.format'),
        ];
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'next_id' => 'required|integer',
            'left_pad' => 'required|numeric',
            'format' => 'required',
        ];
    }
}