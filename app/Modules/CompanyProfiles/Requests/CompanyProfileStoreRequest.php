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

namespace IP\Modules\CompanyProfiles\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyProfileStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return ['company' => trans('ip.company')];
    }

    public function rules()
    {
        return ['company' => 'required|unique:company_profiles,company'];
    }
}