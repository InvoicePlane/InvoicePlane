<?php

namespace IP\Events\Listeners;

use IP\Events\QuoteItemSaving;
use IP\Modules\Quotes\Models\QuoteItem;

class QuoteItemSavingListener
{
    public function handle(QuoteItemSaving $event)
    {
        $item = $event->quoteItem;

        $applyExchangeRate = $item->apply_exchange_rate;
        unset($item->apply_exchange_rate);

        if ($applyExchangeRate == true) {
            $item->price = $item->price * $item->quote->exchange_rate;
        }

        if (!$item->display_order) {
            $displayOrder = QuoteItem::where('quote_id', $item->quote_id)->max('display_order');

            $displayOrder++;

            $item->display_order = $displayOrder;
        }
    }
}
