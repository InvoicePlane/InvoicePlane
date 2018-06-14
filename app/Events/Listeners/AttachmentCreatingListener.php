<?php

namespace IP\Events\Listeners;

use IP\Events\AttachmentCreating;

class AttachmentCreatingListener
{
    public function __construct()
    {
        //
    }

    public function handle(AttachmentCreating $event)
    {
        $event->attachment->url_key = str_random(64);
    }
}
