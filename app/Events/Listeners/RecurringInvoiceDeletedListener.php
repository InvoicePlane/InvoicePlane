<?php

namespace IP\Events\Listeners;

use IP\Events\RecurringInvoiceDeleted;

class RecurringInvoiceDeletedListener
{
    public function __construct()
    {
        //
    }

    public function handle(RecurringInvoiceDeleted $event)
    {
        foreach ($event->recurringInvoice->items as $item) {
            $item->delete();
        }

        $event->recurringInvoice->amount()->delete();
        $event->recurringInvoice->custom()->delete();
    }
}
