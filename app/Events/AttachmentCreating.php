<?php

namespace IP\Events;

use IP\Modules\Attachments\Models\Attachment;
use Illuminate\Queue\SerializesModels;

class AttachmentCreating extends Event
{
    use SerializesModels;

    public function __construct(Attachment $attachment)
    {
        $this->attachment = $attachment;
    }
}
