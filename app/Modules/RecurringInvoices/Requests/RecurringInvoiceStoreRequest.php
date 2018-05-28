<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\RecurringInvoices\Requests;

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
            'company_profile_id'  => trans('fi.company_profile'),
            'client_name'         => trans('fi.client'),
            'user_id'             => trans('fi.user'),
            'next_date'           => trans('fi.start_date'),
            'recurring_frequency' => trans('fi.frequency'),
            'recurring_period'    => trans('fi.frequency'),
            'summary'             => trans('fi.summary'),
            'exchange_rate'       => trans('fi.exchange_rate'),
            'template'            => trans('fi.template'),
            'client_id'           => trans('fi.client'),
            'group_id'            => trans('fi.group'),
            'stop_date'           => trans('fi.stop_date'),
            'items.*.name'        => trans('fi.name'),
            'items.*.quantity'    => trans('fi.quantity'),
            'items.*.price'       => trans('fi.price'),
        ];
    }

    public function rules()
    {
        return [
            'company_profile_id'  => 'required',
            'client_name'         => 'required',
            'user_id'             => 'required',
            'next_date'           => 'required',
            'recurring_frequency' => 'numeric|required',
            'recurring_period'    => 'required',
        ];
    }
}