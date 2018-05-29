<?php

namespace FI\Events;

use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\RecurringInvoices\Models\RecurringInvoice;
use Illuminate\Queue\SerializesModels;

class InvoiceCreatedRecurring extends Event
{
    use SerializesModels;

    public function __construct(Invoice $invoice, RecurringInvoice $recurringInvoice)
    {
        $this->invoice = $invoice;
        $this->recurringInvoice = $recurringInvoice;
    }
}
