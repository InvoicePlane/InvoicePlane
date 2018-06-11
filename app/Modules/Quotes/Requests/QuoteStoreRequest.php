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

namespace FI\Modules\Quotes\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuoteStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'company_profile_id' => trans('ip.company_profile'),
            'client_name' => trans('ip.client'),
            'client_id' => trans('ip.client'),
            'user_id' => trans('ip.user'),
            'summary' => trans('ip.summary'),
            'quote_date' => trans('ip.date'),
            'due_at' => trans('ip.due'),
            'number' => trans('ip.invoice_number'),
            'quote_status_id' => trans('ip.status'),
            'exchange_rate' => trans('ip.exchange_rate'),
            'template' => trans('ip.template'),
            'group_id' => trans('ip.group'),
            'items.*.name' => trans('ip.name'),
            'items.*.quantity' => trans('ip.quantity'),
            'items.*.price' => trans('ip.price'),
        ];
    }

    public function rules()
    {
        return [
            'company_profile_id' => 'required|integer|exists:company_profiles,id',
            'client_name' => 'required',
            'quote_date' => 'required',
            'user_id' => 'required',
        ];
    }
}