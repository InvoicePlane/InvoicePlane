<?php

namespace IP\Events\Listeners;

use IP\Events\QuoteModified;
use IP\Modules\Quotes\Support\QuoteCalculate;

class QuoteModifiedListener
{
    public function __construct(QuoteCalculate $quoteCalculate)
    {
        $this->quoteCalculate = $quoteCalculate;
    }

    public function handle(QuoteModified $event)
    {
        // Calculate the quote and item amounts
        $this->quoteCalculate->calculate($event->quote);
    }
}
