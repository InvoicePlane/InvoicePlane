<?php

namespace IP\Events;

use IP\Modules\Quotes\Models\Quote;
use Illuminate\Queue\SerializesModels;

class QuoteDeleted extends Event
{
    use SerializesModels;

    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }
}
