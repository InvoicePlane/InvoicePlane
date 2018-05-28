<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\CustomFields\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomFieldStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'tbl_name'    => trans('fi.table_name'),
            'field_label' => trans('fi.field_label'),
            'field_type'  => trans('fi.field_type'),
        ];
    }

    public function rules()
    {
        return [
            'tbl_name'    => 'required',
            'field_label' => 'required',
            'field_type'  => 'required',
        ];
    }
}