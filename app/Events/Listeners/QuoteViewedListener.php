<?php

namespace FI\Events\Listeners;

use FI\Events\QuoteViewed;

class QuoteViewedListener
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
     * @param  QuoteViewed $event
     * @return void
     */
    public function handle(QuoteViewed $event)
    {
        if (request('disableFlag') != 1)
        {
            if (auth()->guest() or auth()->user()->user_type == 'client')
            {
                $event->quote->activities()->create(['activity' => 'public.viewed']);
                $event->quote->viewed = 1;
                $event->quote->save();
            }
        }
    }
}
