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

namespace FI\Modules\RecurringInvoices\Requests;

use FI\Support\NumberFormatter;

class RecurringInvoiceUpdateRequest extends RecurringInvoiceStoreRequest
{
    public function prepareForValidation()
    {
        $request = $this->all();

        if (isset($request['items']))
        {
            foreach ($request['items'] as $key => $item)
            {
                $request['items'][$key]['quantity'] = NumberFormatter::unformat($item['quantity']);
                $request['items'][$key]['price']    = NumberFormatter::unformat($item['price']);
            }
        }

        $this->replace($request);
    }

    public function rules()
    {
        return [
            'summary'             => 'max:255',
            'exchange_rate'       => 'required|numeric',
            'template'            => 'required',
            'next_date'           => 'required_without:stop_date',
            'recurring_frequency' => 'numeric|required',
            'recurring_period'    => 'required',
            'items.*.name'        => 'required_with:items.*.price,items.*.quantity',
            'items.*.quantity'    => 'required_with:items.*.price,items.*.name|numeric',
            'items.*.price'       => 'required_with:items.*.name,items.*.quantity|numeric',
        ];
    }
}