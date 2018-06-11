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

namespace FI\Modules\Setup\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'user.name' => trans('ip.name'),
            'user.email' => trans('ip.email'),
            'user.password' => trans('ip.password'),
            'company_profile.company' => trans('ip.company_profile'),
        ];
    }

    public function rules()
    {
        return [
            'user.name' => 'required',
            'user.email' => 'required|email',
            'user.password' => 'required|confirmed',
            'company_profile.company' => 'required',
        ];
    }
}