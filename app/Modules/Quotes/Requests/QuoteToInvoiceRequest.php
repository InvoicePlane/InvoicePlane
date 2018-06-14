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

namespace IP\Modules\Quotes\Requests;

class QuoteToInvoiceRequest extends QuoteStoreRequest
{
    public function rules()
    {
        return [
            'client_id' => 'required',
            'invoice_date' => 'required',
            'group_id' => 'required',
        ];
    }
}