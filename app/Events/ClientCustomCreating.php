<?php

namespace FI\Events;

use FI\Modules\CustomFields\Models\ClientCustom;
use Illuminate\Queue\SerializesModels;

class ClientCustomCreating extends Event
{
    use SerializesModels;

    public function __construct(ClientCustom $clientCustom)
    {
        $this->clientCustom = $clientCustom;
    }
}
