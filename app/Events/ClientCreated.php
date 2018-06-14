<?php

namespace IP\Events;

use IP\Modules\Clients\Models\Client;
use Illuminate\Queue\SerializesModels;

class ClientCreated extends Event
{
    use SerializesModels;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}