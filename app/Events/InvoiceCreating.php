<?php

namespace FI\Events;

use FI\Modules\Invoices\Models\Invoice;
use Illuminate\Queue\SerializesModels;

class InvoiceCreating extends Event
{
    use SerializesModels;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }
}
