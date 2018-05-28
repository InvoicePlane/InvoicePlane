<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
            'email'    => trans('fi.email'),
            'password' => trans('fi.password'),
            'name'     => trans('fi.name'),
        ];
    }

    public function rules()
    {
        return [
            'email'    => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'name'     => 'required',
        ];
    }
}