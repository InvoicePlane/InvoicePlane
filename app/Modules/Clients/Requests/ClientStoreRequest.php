<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Clients\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'name'        => trans('fi.name'),
            'unique_name' => trans('fi.unique_name'),
            'email'       => trans('fi.email'),
        ];
    }

    public function prepareForValidation()
    {
        $request = $this->all();

        $request['email'] = $this->input('client_email', $this->input('email', ''));

        unset($request['client_email']);

        $this->replace($request);
    }

    public function rules()
    {
        return [
            'name'        => 'required',
            'unique_name' => 'required_with:name|unique:clients',
            'email'       => 'email',
        ];
    }
}