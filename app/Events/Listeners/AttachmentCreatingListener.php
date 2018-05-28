<?php

namespace FI\Events\Listeners;

use FI\Events\AttachmentCreating;

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
