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

namespace IP\Composers;

use IP\Modules\Currencies\Models\Currency;
use IP\Modules\Invoices\Support\InvoiceTemplates;
use IP\Modules\Quotes\Support\QuoteTemplates;
use IP\Support\Languages;

class ClientFormComposer
{
    public function compose($view)
    {
        $view->with('currencies', Currency::getList())
            ->with('invoiceTemplates', InvoiceTemplates::lists())
            ->with('quoteTemplates', QuoteTemplates::lists())
            ->with('languages', Languages::listLanguages());
    }
}
