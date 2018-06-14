<?php

namespace IP\Events\Listeners;

use IP\Events\RecurringInvoiceModified;
use IP\Modules\RecurringInvoices\Support\RecurringInvoiceCalculate;

class RecurringInvoiceModifiedListener
{
    private $recurringInvoiceCalculate;

    public function __construct(RecurringInvoiceCalculate $recurringInvoiceCalculate)
    {
        $this->recurringInvoiceCalculate = $recurringInvoiceCalculate;
    }

    public function handle(RecurringInvoiceModified $event)
    {
        $this->recurringInvoiceCalculate->calculate($event->recurringInvoice->id);
    }
}
