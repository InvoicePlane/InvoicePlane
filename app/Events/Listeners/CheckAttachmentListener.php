<?php

namespace IP\Events\Listeners;

use IP\Events\CheckAttachment;

class CheckAttachmentListener
{
    public function handle(CheckAttachment $event)
    {
        if (request()->hasFile('attachments')) {
            foreach (request()->file('attachments') as $attachment) {
                if ($attachment) {
                    $event->object->attachments()->create([
                        'user_id' => auth()->user()->id,
                        /*
                         *  Wasn't posted with the upload form, give it a default value
                         **/
                        'client_visibility' => 0,
                        'filename' => $attachment->getClientOriginalName(),
                        'mimetype' => $attachment->getMimeType(),
                        'size' => $attachment->getSize(),
                    ]);

                    $attachment->move($event->object->attachment_path, $attachment->getClientOriginalName());
                }

            }
        }
    }
}
