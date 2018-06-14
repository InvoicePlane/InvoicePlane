<?php

namespace IP\Events;

use IP\Modules\Invoices\Models\InvoiceItem;
use Illuminate\Queue\SerializesModels;

class InvoiceItemSaving extends Event
{
    use SerializesModels;

    public function __construct(InvoiceItem $invoiceItem)
    {
        $this->invoiceItem = $invoiceItem;
    }
}
