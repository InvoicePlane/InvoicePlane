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

namespace FI\Modules\Users\Requests;

class UserUpdateRequest extends UserStoreRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email|unique:users,email,' . $this->route('id'),
            'name' => 'required',
        ];
    }
}