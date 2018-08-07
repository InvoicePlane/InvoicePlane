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

function formatAddress($object)
{
    if ($object->address or $object->city or $object->state or $object->zip or $object->country) {
        $address = config('ip.addressFormat');

        $address = str_replace('{{ address }}', $object->address, $address);
        $address = str_replace('{{ city }}', $object->city, $address);
        $address = str_replace('{{ state }}', $object->state, $address);
        $address = str_replace('{{ zip }}', $object->zip, $address);
        $address = str_replace('{{ zip_code }}', $object->zip, $address);
        $address = str_replace('{{ postal_code }}', $object->zip, $address);
        $address = str_replace('{{ country }}', $object->country, $address);

        return $address;
    }

    return '';
}
