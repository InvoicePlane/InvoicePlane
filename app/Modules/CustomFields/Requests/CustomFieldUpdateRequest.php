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

class CustomFieldUpdateRequest extends CustomFieldStoreRequest
{
    public function rules()
    {
        return [
            'field_label' => 'required',
            'field_type'  => 'required',
        ];
    }
}