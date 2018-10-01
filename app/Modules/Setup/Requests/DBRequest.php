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

namespace IP\Modules\Setup\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DBRequest extends FormRequest
{
    protected $redirectRoute = 'setup.dbconfig';

    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'db_connection' => trans('ip.database_connection'),
            'db_host' => trans('ip.database_host'),
            'db_port' => trans('ip.database_port'),
            'db_database' => trans('ip.database_database'),
            'db_username' => trans('ip.database_user'),
            'db_password' => trans('ip.database_pass'),
            'db_prefix' => trans('ip.database_prefix'),
        ];
    }

    public function rules()
    {
        return [
            'db_connection' => 'required|alpha_dash',
            'db_host' => 'required_unless:db_connection,sqlite',
            'db_port' => 'required_unless:db_connection,sqlite|numeric',
            'db_database' => 'required|alpha_dash',
            'db_username' => 'required_unless:db_connection,sqlite',
            'db_password' => 'required_unless:db_connection,sqlite',
        ];
    }
}