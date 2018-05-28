<?php

namespace FI\Events\Listeners;

use FI\Events\InvoiceModified;
use FI\Modules\Invoices\Support\InvoiceCalculate;

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
