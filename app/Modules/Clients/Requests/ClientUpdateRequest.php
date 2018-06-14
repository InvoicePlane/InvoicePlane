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

namespace IP\Modules\Clients\Requests;

class ClientUpdateRequest extends ClientStoreRequest
{
    public function rules()
    {
        $rules = parent::rules();

        $rules['unique_name'] = 'required|unique:clients,unique_name,' . $this->route('id');

        return $rules;
    }
}