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

namespace FI\Modules\Settings\Requests;

use FI\Modules\Settings\Rules\ValidFile;
use Illuminate\Foundation\Http\FormRequest;

class SettingUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'setting.invoicesDueAfter'  => trans('fi.invoices_due_after'),
            'setting.quotesExpireAfter' => trans('fi.quotes_expire_after'),
            'setting.pdfBinaryPath'     => trans('fi.binary_path'),
        ];
    }

    public function rules()
    {
        $rules = [
            'setting.invoicesDueAfter'  => 'required|numeric',
            'setting.quotesExpireAfter' => 'required|numeric',
            'setting.pdfBinaryPath'     => ['required_if:setting.pdfDriver,wkhtmltopdf', new ValidFile],
        ];

        foreach (config('fi.settingValidationRules') as $settingValidationRules)
        {
            $rules = array_merge($rules, $settingValidationRules['rules']);
        }

        return $rules;
    }
}