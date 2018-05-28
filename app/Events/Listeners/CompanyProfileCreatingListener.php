<?php

namespace FI\Events\Listeners;

use FI\Events\CompanyProfileCreating;

class CompanyProfileCreatingListener
{
    public function __construct()
    {
        //
    }

    public function handle(CompanyProfileCreating $event)
    {
        if (!$event->companyProfile->invoice_template)
        {
            $event->companyProfile->invoice_template = config('fi.invoiceTemplate');
        }

        if (!$event->companyProfile->quote_template)
        {
            $event->companyProfile->quote_template = config('fi.quoteTemplate');
        }
    }
}
