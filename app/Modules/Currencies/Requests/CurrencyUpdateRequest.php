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

namespace FI\Modules\Currencies\Requests;

class CurrencyUpdateRequest extends CurrencyStoreRequest
{
    public function rules()
    {
        return [
            'name'      => 'required',
            'code'      => 'required|unique:currencies,code,' . $this->route('id'),
            'symbol'    => 'required',
            'placement' => 'required',
        ];
    }
}