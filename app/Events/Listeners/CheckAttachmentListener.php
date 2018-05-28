<?php

namespace FI\Events\Listeners;

use FI\Events\CheckAttachment;

class CheckAttachmentListener
{
    public function handle(CheckAttachment $event)
    {
        if (request()->hasFile('attachments'))
        {
            foreach (request()->file('attachments') as $attachment)
            {
                if ($attachment)
                {
                    $event->object->attachments()->create([
                        'user_id'  => auth()->user()->id,
                        'filename' => $attachment->getClientOriginalName(),
                        'mimetype' => $attachment->getMimeType(),
                        'size'     => $attachment->getSize(),
                    ]);

                    $attachment->move($event->object->attachment_path, $attachment->getClientOriginalName());
                }

            }
        }
    }
}
