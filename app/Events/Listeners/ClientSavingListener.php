<?php

namespace FI\Events\Listeners;

use FI\Events\ClientSaving;

class ClientSavingListener
{
    public function __construct()
    {
        //
    }

    public function handle(ClientSaving $event)
    {
        $event->client->name = strip_tags($event->client->name);
        $event->client->address = strip_tags($event->client->address);

        if (!$event->client->unique_name) {
            $event->client->unique_name = $event->client->name;
        }
    }
}
