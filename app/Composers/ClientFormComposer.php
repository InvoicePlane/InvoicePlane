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

use FI\Modules\Currencies\Models\Currency;
use FI\Modules\Invoices\Support\InvoiceTemplates;
use FI\Modules\Quotes\Support\QuoteTemplates;
use FI\Support\Languages;

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
