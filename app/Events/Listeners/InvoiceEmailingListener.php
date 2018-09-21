<?php

namespace IP\Events\Listeners;

use IP\Events\InvoiceEmailing;
use IP\Support\DateFormatter;

class InvoiceEmailingListener
{
    public function handle(InvoiceEmailing $event)
    {
        if (config('ip.resetInvoiceDateEmailDraft') and $event->invoice->status_text == 'draft') {
            $event->invoice->invoice_date = date('Y-m-d');
            $event->invoice->due_at = DateFormatter::incrementDateByDays(date('Y-m-d'), config('ip.invoicesDueAfter'));
            $event->invoice->save();
        }
    }
}
