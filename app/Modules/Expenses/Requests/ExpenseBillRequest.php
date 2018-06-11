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

namespace FI\Modules\Expenses\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseBillRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'invoice_id' => trans('ip.invoice'),
            'item_name' => trans('ip.item'),
        ];
    }

    public function rules()
    {
        return [
            'invoice_id' => 'required',
            'item_name' => 'required',
        ];
    }
}