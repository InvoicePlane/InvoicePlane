<?php

namespace IP\Events;

use IP\Modules\Quotes\Models\Quote;
use Illuminate\Queue\SerializesModels;

class QuoteViewed extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }
}
