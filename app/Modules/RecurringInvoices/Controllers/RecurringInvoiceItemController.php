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

namespace IP\Modules\RecurringInvoices\Controllers;

use IP\Http\Controllers\Controller;
use IP\Modules\RecurringInvoices\Models\RecurringInvoiceItem;

class RecurringInvoiceItemController extends Controller
{
    public function delete()
    {
        RecurringInvoiceItem::destroy(request('id'));
    }
}