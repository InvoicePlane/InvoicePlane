<?php

namespace FI\Events\Listeners;

use FI\Events\UserDeleted;

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
