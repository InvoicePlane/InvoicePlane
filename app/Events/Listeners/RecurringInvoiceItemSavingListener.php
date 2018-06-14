<?php

namespace IP\Events\Listeners;

use IP\Events\RecurringInvoiceItemSaving;
use IP\Modules\RecurringInvoices\Models\RecurringInvoiceItem;

class RecurringInvoiceItemSavingListener
{
    public function handle(RecurringInvoiceItemSaving $event)
    {
        $item = $event->recurringInvoiceItem;

        $applyExchangeRate = $item->apply_exchange_rate;
        unset($item->apply_exchange_rate);

        if ($applyExchangeRate == true) {
            $item->price = $item->price * $item->invoice->exchange_rate;
        }

        if (!$item->display_order) {
            $displayOrder = RecurringInvoiceItem::where('invoice_id', $item->recurring_invoice_id)->max('display_order');

            $displayOrder++;

            $item->display_order = $displayOrder;
        }
    }
}
