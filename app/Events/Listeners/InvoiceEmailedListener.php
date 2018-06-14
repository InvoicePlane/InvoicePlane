<?php

namespace IP\Events\Listeners;

use IP\Events\InvoiceEmailed;
use IP\Support\Statuses\InvoiceStatuses;

class InvoiceEmailedListener
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
     * @param  InvoiceEmailed $event
     * @return void
     */
    public function handle(InvoiceEmailed $event)
    {
        // Change the status to sent if the status is currently draft
        if ($event->invoice->invoice_status_id == InvoiceStatuses::getStatusId('draft')) {
            $event->invoice->invoice_status_id = InvoiceStatuses::getStatusId('sent');
            $event->invoice->save();
        }
    }
}
