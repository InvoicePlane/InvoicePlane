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

namespace FI\Composers;

use FI\Support\Statuses\InvoiceStatuses;

class InvoiceTableComposer
{
    public function compose($view)
    {
        $view->with('statuses', InvoiceStatuses::statuses());
    }
}
