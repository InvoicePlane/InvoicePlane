<?php

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