<?php

namespace FI\Events\Listeners;

use FI\Events\InvoiceViewed;

class InvoiceViewedListener
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
     * @param  InvoiceViewed $event
     * @return void
     */
    public function handle(InvoiceViewed $event)
    {
        if (request('disableFlag') != 1)
        {
            if (auth()->guest() or auth()->user()->user_type == 'client')
            {
                $event->invoice->activities()->create(['activity' => 'public.viewed']);
                $event->invoice->viewed = 1;
                $event->invoice->save();
            }
        }
    }
}
