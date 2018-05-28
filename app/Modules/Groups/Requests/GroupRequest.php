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

namespace FI\Modules\Groups\Requests;

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
            'name'     => trans('fi.name'),
            'next_id'  => trans('fi.next_number'),
            'left_pad' => trans('fi.left_pad'),
            'format'   => trans('fi.format'),
        ];
    }

    public function rules()
    {
        return [
            'name'     => 'required',
            'next_id'  => 'required|integer',
            'left_pad' => 'required|numeric',
            'format'   => 'required',
        ];
    }
}