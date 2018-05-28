<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\TaxRates\Requests;

use FI\Support\NumberFormatter;
use Illuminate\Foundation\Http\FormRequest;

class TaxRateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'name'    => trans('fi.name'),
            'percent' => trans('fi.percent'),
        ];
    }

    public function prepareForValidation()
    {
        $request = $this->all();

        $request['percent'] = NumberFormatter::unformat($request['percent']);

        $this->replace($request);
    }

    public function rules()
    {
        return [
            'name'    => 'required',
            'percent' => 'required|numeric',
        ];
    }
}