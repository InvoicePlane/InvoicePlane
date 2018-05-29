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

namespace FI\Modules\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'email' => trans('fi.email'),
            'password' => trans('fi.password'),
            'name' => trans('fi.name'),
        ];
    }

    public function rules()
    {
        return [
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'name' => 'required',
        ];
    }
}