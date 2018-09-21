<?php

namespace IP\Events\Listeners;

use IP\Events\CompanyProfileCreating;

class CompanyProfileCreatingListener
{
    public function __construct()
    {
        //
    }

    public function handle(CompanyProfileCreating $event)
    {
        if (!$event->companyProfile->invoice_template) {
            $event->companyProfile->invoice_template = config('ip.invoiceTemplate');
        }

        if (!$event->companyProfile->quote_template) {
            $event->companyProfile->quote_template = config('ip.quoteTemplate');
        }
    }
}
