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

namespace IP\Support\Statuses;

class InvoiceStatuses extends AbstractStatuses
{
    protected static $statuses = [
        '0' => 'all_statuses',
        '1' => 'draft',
        '2' => 'sent',
        '3' => 'paid',
        '4' => 'canceled',
    ];
}