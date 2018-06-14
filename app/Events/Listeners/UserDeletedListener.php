<?php

namespace IP\Events\Listeners;

use IP\Events\UserDeleted;

class UserDeletedListener
{
    public function __construct()
    {
        //
    }

    public function handle(UserDeleted $event)
    {
        $event->user->custom()->delete();
    }
}
