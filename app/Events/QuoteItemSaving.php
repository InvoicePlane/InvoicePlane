<?php

namespace FI\Events;

use FI\Modules\Quotes\Models\QuoteItem;
use Illuminate\Queue\SerializesModels;

class QuoteItemSaving extends Event
{
    use SerializesModels;

    public function __construct(QuoteItem $quoteItem)
    {
        $this->quoteItem = $quoteItem;
    }
}
