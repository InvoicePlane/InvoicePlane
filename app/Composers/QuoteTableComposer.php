<?php

namespace FI\Composers;

use FI\Support\Statuses\QuoteStatuses;

class QuoteTableComposer
{
    public function compose($view)
    {
        $view->with('statuses', QuoteStatuses::statuses());
    }
}