<?php

namespace IP\Events;

use IP\Modules\Users\Models\User;
use Illuminate\Queue\SerializesModels;

class UserDeleted extends Event
{
    use SerializesModels;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
