<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Groups\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'name'     => trans('fi.name'),
            'next_id'  => trans('fi.next_number'),
            'left_pad' => trans('fi.left_pad'),
            'format'   => trans('fi.format'),
        ];
    }

    public function rules()
    {
        return [
            'name'     => 'required',
            'next_id'  => 'required|integer',
            'left_pad' => 'required|numeric',
            'format'   => 'required',
        ];
    }
}