<?php

namespace FI\Events\Listeners;

use FI\Events\ClientCreated;
use FI\Modules\CustomFields\Models\ClientCustom;

class ClientCreatedListener
{
    public function __construct()
    {
        //
    }

    public function handle(ClientCreated $event)
    {
        // Create the default custom record.
        $event->client->custom()->save(new ClientCustom());
    }
}
