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

namespace FI\Modules\Invoices\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'company_profile_id' => trans('fi.company_profile'),
            'client_name' => trans('fi.client'),
            'client_id' => trans('fi.client'),
            'user_id' => trans('fi.user'),
            'summary' => trans('fi.summary'),
            'invoice_date' => trans('fi.date'),
            'due_at' => trans('fi.due'),
            'number' => trans('fi.invoice_number'),
            'invoice_status_id' => trans('fi.status'),
            'exchange_rate' => trans('fi.exchange_rate'),
            'template' => trans('fi.template'),
            'group_id' => trans('fi.group'),
            'items.*.name' => trans('fi.name'),
            'items.*.quantity' => trans('fi.quantity'),
            'items.*.price' => trans('fi.price'),
        ];
    }

    public function rules()
    {
        return [
            'company_profile_id' => 'required|integer|exists:company_profiles,id',
            'client_name' => 'required',
            'invoice_date' => 'required',
            'user_id' => 'required',
        ];
    }
}