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

namespace IP\Modules\Exports\Support\Results;

use IP\Modules\Clients\Models\Client;

class Clients implements SourceInterface
{
    public function getResults($params = [])
    {
        $client = Client::orderBy('name');

        return $client->get()->toArray();
    }
}