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

class DBRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'db_host' => trans('ip.db_host'),
            'db_port' => trans('ip.db_port'),
            'db_database' => trans('ip.db_database'),
            'db_username' => trans('ip.db_user'),
            'db_password' => trans('ip.db_pass'),
            'db_prefix' => trans('ip.db_prefix'),
        ];
    }

    public function rules()
    {
        return [
            'db_host' => 'required',
            'db_port' => 'required|numeric',
            'db_database' => 'required',
            'db_username' => 'required',
            'db_password' => 'required',
        ];
    }
}