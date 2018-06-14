<?php

namespace IP\Events\Listeners;

use IP\Events\InvoiceModified;
use IP\Modules\Invoices\Support\InvoiceCalculate;

class InvoiceModifiedListener
{
    public function __construct(InvoiceCalculate $invoiceCalculate)
    {
        $this->invoiceCalculate = $invoiceCalculate;
    }

    public function handle(InvoiceModified $event)
    {
        $this->invoiceCalculate->calculate($event->invoice);
    }
}
