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

namespace IP\Modules\RecurringInvoices\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecurringInvoiceStoreRequest extends FormRequest
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
            'user_id' => trans('ip.user'),
            'next_date' => trans('ip.start_date'),
            'recurring_frequency' => trans('ip.frequency'),
            'recurring_period' => trans('ip.frequency'),
            'summary' => trans('ip.summary'),
            'exchange_rate' => trans('ip.exchange_rate'),
            'template' => trans('ip.template'),
            'client_id' => trans('ip.client'),
            'group_id' => trans('ip.group'),
            'stop_date' => trans('ip.stop_date'),
            'items.*.name' => trans('ip.name'),
            'items.*.quantity' => trans('ip.quantity'),
            'items.*.price' => trans('ip.price'),
        ];
    }

    public function rules()
    {
        return [
            'company_profile_id' => 'required',
            'client_name' => 'required',
            'user_id' => 'required',
            'next_date' => 'required',
            'recurring_frequency' => 'numeric|required',
            'recurring_period' => 'required',
        ];
    }
}