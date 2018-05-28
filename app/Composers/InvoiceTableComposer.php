<?php

namespace FI\Composers;

use FI\Support\Statuses\InvoiceStatuses;

class InvoiceTableComposer
{
    public function compose($view)
    {
        $view->with('statuses', InvoiceStatuses::statuses());
    }
}