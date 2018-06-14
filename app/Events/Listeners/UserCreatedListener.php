<?php

namespace IP\Events\Listeners;

use IP\Events\UserCreated;
use IP\Modules\CustomFields\Models\UserCustom;

class UserCreatedListener
{
    public function __construct()
    {
        //
    }

    public function handle(UserCreated $event)
    {
        // Create the default custom record.
        $event->user->custom()->save(new UserCustom());
    }
}
