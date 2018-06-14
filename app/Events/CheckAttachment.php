<?php

namespace IP\Events;

use Illuminate\Queue\SerializesModels;

class CheckAttachment extends Event
{
    use SerializesModels;

    public function __construct($object)
    {
        $this->object = $object;
    }

}
