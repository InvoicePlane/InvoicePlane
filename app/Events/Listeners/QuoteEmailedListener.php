<?php

namespace FI\Events\Listeners;

use FI\Events\QuoteEmailed;
use FI\Support\Statuses\QuoteStatuses;

class QuoteEmailedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  QuoteEmailed $event
     * @return void
     */
    public function handle(QuoteEmailed $event)
    {
        // Change the status to sent if the status is currently draft
        if ($event->quote->quote_status_id == QuoteStatuses::getStatusId('draft'))
        {
            $event->quote->quote_status_id = QuoteStatuses::getStatusId('sent');
            $event->quote->save();
        }
    }
}
