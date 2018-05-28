<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class SendEmailRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'email' => trans('fi.email'),
        ];
    }

    public function rules()
    {
        Validator::extend('emails', function ($attribute, $value, $parameters)
        {
            foreach ($value as $email)
            {
                $data = ['email' => trim($email)];

                $validator = Validator::make($data, ['email' => 'required|email']);

                if ($validator->fails())
                {
                    return false;
                }
            }

            return true;
        }, trans('fi.multiple_email_validation'));

        $rules = [
            'subject' => 'required',
            'body'    => 'required',
            'to'      => 'required|emails',
            'cc'      => 'emails',
            'bcc'     => 'emails',
        ];

        return $rules;
    }
}