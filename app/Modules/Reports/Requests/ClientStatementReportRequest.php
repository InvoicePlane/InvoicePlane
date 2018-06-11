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

namespace FI\Modules\Reports\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientStatementReportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'from_date' => trans('ip.from_date'),
            'to_date' => trans('ip.to_date'),
            'client_name' => trans('ip.client'),
        ];
    }

    public function rules()
    {
        return [
            'from_date' => 'required',
            'to_date' => 'required',
            'client_name' => 'required|exists:clients,unique_name',
        ];
    }
}