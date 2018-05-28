<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Invoices\Requests;

use FI\Support\NumberFormatter;

class InvoiceUpdateRequest extends InvoiceStoreRequest
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
            'summary'           => 'max:255',
            'invoice_date'      => 'required',
            'due_at'            => 'required',
            'number'            => 'required',
            'invoice_status_id' => 'required',
            'exchange_rate'     => 'required|numeric',
            'template'          => 'required',
            'items.*.name'      => 'required_with:items.*.price,items.*.quantity',
            'items.*.quantity'  => 'required_with:items.*.price,items.*.name|numeric',
            'items.*.price'     => 'required_with:items.*.name,items.*.quantity|numeric',
        ];
    }
}