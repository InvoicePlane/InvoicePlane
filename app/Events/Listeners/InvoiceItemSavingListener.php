<?php

namespace IP\Events\Listeners;

use IP\Events\InvoiceItemSaving;
use IP\Modules\Invoices\Models\InvoiceItem;

class InvoiceItemSavingListener
{
    public function handle(InvoiceItemSaving $event)
    {
        $item = $event->invoiceItem;

        $applyExchangeRate = $item->apply_exchange_rate;
        unset($item->apply_exchange_rate);

        if ($applyExchangeRate == true) {
            $item->price = $item->price * $item->invoice->exchange_rate;
        }

        if (!$item->display_order) {
            $displayOrder = InvoiceItem::where('invoice_id', $item->invoice_id)->max('display_order');

            $displayOrder++;

            $item->display_order = $displayOrder;
        }

        if (is_null($item->tax_rate_id)) {
            $item->tax_rate_id = 0;
        }

        if (is_null($item->tax_rate_2_id)) {
            $item->tax_rate_2_id = 0;
        }
    }
}
