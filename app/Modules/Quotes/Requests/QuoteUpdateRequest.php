<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Quotes\Requests;

use FI\Support\NumberFormatter;

class QuoteUpdateRequest extends QuoteStoreRequest
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
            'summary'          => 'max:255',
            'quote_date'       => 'required',
            'number'           => 'required',
            'quote_status_id'  => 'required',
            'exchange_rate'    => 'required|numeric',
            'template'         => 'required',
            'items.*.name'     => 'required_with:items.*.price,items.*.quantity',
            'items.*.quantity' => 'required_with:items.*.price,items.*.name|numeric',
            'items.*.price'    => 'required_with:items.*.name,items.*.quantity|numeric',
        ];
    }
}