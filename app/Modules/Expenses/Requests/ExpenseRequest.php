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

use FI\Support\NumberFormatter;
use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'user_id' => trans('ip.user'),
            'company_profile_id' => trans('ip.company_profile'),
            'expense_date' => trans('ip.date'),
            'category_name' => trans('ip.category'),
            'description' => trans('ip.description'),
            'amount' => trans('ip.amount'),
        ];
    }

    public function prepareForValidation()
    {
        $request = $this->all();

        if (isset($request['amount'])) {
            $request['amount'] = NumberFormatter::unformat($request['amount']);
        }

        $this->replace($request);
    }

    public function rules()
    {
        return [
            'user_id' => 'required',
            'company_profile_id' => 'required',
            'expense_date' => 'required',
            'category_name' => 'required',
            'description' => 'max:255',
            'amount' => 'required|numeric',
        ];
    }
}