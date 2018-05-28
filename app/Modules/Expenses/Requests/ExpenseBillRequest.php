<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
            'invoice_id' => trans('fi.invoice'),
            'item_name'  => trans('fi.item'),
        ];
    }

    public function rules()
    {
        return [
            'invoice_id' => 'required',
            'item_name'  => 'required',
        ];
    }
}