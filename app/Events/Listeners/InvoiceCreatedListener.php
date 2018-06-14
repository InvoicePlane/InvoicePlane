<?php

namespace IP\Events\Listeners;

use IP\Events\InvoiceCreated;
use IP\Modules\CustomFields\Models\InvoiceCustom;
use IP\Modules\Groups\Models\Group;
use IP\Modules\Invoices\Support\InvoiceCalculate;

class InvoiceCreatedListener
{
    private $invoiceCalculate;

    public function __construct(InvoiceCalculate $invoiceCalculate)
    {
        $this->invoiceCalculate = $invoiceCalculate;
    }

    public function handle(InvoiceCreated $event)
    {
        // Create the empty invoice amount record.
        $this->invoiceCalculate->calculate($event->invoice);

        // Increment the next id.
        Group::incrementNextId($event->invoice);

        // Create the custom invoice record.
        $event->invoice->custom()->save(new InvoiceCustom());
    }
}
