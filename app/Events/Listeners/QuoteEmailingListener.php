<?php

namespace IP\Events\Listeners;

use IP\Events\QuoteEmailing;
use IP\Support\DateFormatter;

class QuoteEmailingListener
{
    public function handle(QuoteEmailing $event)
    {
        if (config('ip.resetQuoteDateEmailDraft') and $event->quote->status_text == 'draft') {
            $event->quote->quote_date = date('Y-m-d');
            $event->quote->expires_at = DateFormatter::incrementDateByDays(date('Y-m-d'), config('ip.quotesExpireAfter'));
            $event->quote->save();
        }
    }
}
