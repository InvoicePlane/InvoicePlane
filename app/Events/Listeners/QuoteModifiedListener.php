<?php

namespace FI\Events\Listeners;

use FI\Events\QuoteModified;
use FI\Modules\Quotes\Support\QuoteCalculate;

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
