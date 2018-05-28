<?php

namespace FI\Events\Listeners;

use FI\Events\QuoteCreated;
use FI\Modules\CustomFields\Models\QuoteCustom;
use FI\Modules\Groups\Models\Group;
use FI\Modules\Quotes\Support\QuoteCalculate;

class QuoteCreatedListener
{
    public function __construct(QuoteCalculate $quoteCalculate)
    {
        $this->quoteCalculate = $quoteCalculate;
    }

    public function handle(QuoteCreated $event)
    {
        // Create the empty quote amount record
        $this->quoteCalculate->calculate($event->quote);

        // Increment the next id
        Group::incrementNextId($event->quote);

        // Create the custom quote record.
        $event->quote->custom()->save(new QuoteCustom());
    }
}
