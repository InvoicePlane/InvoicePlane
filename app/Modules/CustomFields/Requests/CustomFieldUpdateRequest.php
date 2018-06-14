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

namespace IP\Modules\CustomFields\Requests;

class CustomFieldUpdateRequest extends CustomFieldStoreRequest
{
    public function rules()
    {
        return [
            'field_label' => 'required',
            'field_type' => 'required',
        ];
    }
}