<?php

namespace FI\Events\Listeners;

use FI\Events\RecurringInvoiceCreated;
use FI\Modules\CustomFields\Models\RecurringInvoiceCustom;
use FI\Modules\RecurringInvoices\Support\RecurringInvoiceCalculate;

class RecurringInvoiceCreatedListener
{
    private $recurringInvoiceCalculate;

    public function __construct(RecurringInvoiceCalculate $recurringInvoiceCalculate)
    {
        $this->recurringInvoiceCalculate = $recurringInvoiceCalculate;
    }

    public function handle(RecurringInvoiceCreated $event)
    {
        // Create the empty invoice amount record.
        $this->recurringInvoiceCalculate->calculate($event->recurringInvoice->id);

        // Create the custom record.
        $event->recurringInvoice->custom()->save(new RecurringInvoiceCustom());
    }
}
