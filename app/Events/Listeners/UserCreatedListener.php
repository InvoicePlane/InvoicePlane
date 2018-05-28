<?php

namespace FI\Events\Listeners;

use FI\Events\UserCreated;
use FI\Modules\CustomFields\Models\UserCustom;

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
