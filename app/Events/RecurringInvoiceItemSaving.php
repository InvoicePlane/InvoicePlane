<?php

namespace IP\Events;

use IP\Modules\RecurringInvoices\Models\RecurringInvoiceItem;
use Illuminate\Queue\SerializesModels;

class RecurringInvoiceItemSaving extends Event
{
    use SerializesModels;

    public function __construct(RecurringInvoiceItem $recurringInvoiceItem)
    {
        $this->recurringInvoiceItem = $recurringInvoiceItem;
    }
}
