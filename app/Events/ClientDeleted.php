<?php

namespace IP\Events;

use IP\Modules\Clients\Models\Client;
use Illuminate\Queue\SerializesModels;

class ClientDeleted extends Event
{
    use SerializesModels;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}
