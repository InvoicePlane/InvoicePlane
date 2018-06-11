<?php

namespace FI\Events\Listeners;

use FI\Events\NoteCreated;
use FI\Modules\MailQueue\Support\MailQueue;

class NoteCreatedListener
{
    public function __construct(MailQueue $mailQueue)
    {
        $this->mailQueue = $mailQueue;
    }

    public function handle(NoteCreated $event)
    {
        $mail = $this->mailQueue->create($event->note->notable, [
            'to' => [$event->note->notable->user->email],
            'cc' => [config('fi.mailDefaultCc')],
            'bcc' => [config('fi.mailDefaultBcc')],
            'subject' => trans('ip.note_notification'),
            'body' => $event->note->formatted_note,
            'attach_pdf' => config('fi.attachPdf'),
        ]);

        $this->mailQueue->send($mail->id);
    }
}
