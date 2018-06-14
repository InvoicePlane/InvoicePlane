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

namespace IP\Modules\API\Requests;

use IP\Modules\Quotes\Requests\QuoteStoreRequest;

class APIQuoteStoreRequest extends QuoteStoreRequest
{
    public function rules()
    {
        $rules = parent::rules();

        unset($rules['user_id']);

        return $rules;
    }
}