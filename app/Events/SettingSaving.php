<?php

namespace FI\Events;

use FI\Modules\Settings\Models\Setting;
use Illuminate\Queue\SerializesModels;

class SettingSaving extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }
}
