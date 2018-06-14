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

namespace IP\Modules\API\Controllers;

class ApiKeyController extends ApiController
{
    public function generateKeys()
    {
        return response()->json(['api_public_key' => str_random(32), 'api_secret_key' => str_random(32)]);
    }
}