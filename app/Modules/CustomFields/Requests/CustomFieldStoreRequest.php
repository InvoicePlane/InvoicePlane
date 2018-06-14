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

namespace IP\Modules\CustomFields\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomFieldStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'tbl_name' => trans('ip.table_name'),
            'field_label' => trans('ip.field_label'),
            'field_type' => trans('ip.field_type'),
        ];
    }

    public function rules()
    {
        return [
            'tbl_name' => 'required',
            'field_label' => 'required',
            'field_type' => 'required',
        ];
    }
}