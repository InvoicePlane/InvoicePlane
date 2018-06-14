<?php

namespace IP\Events\Listeners;

use IP\Events\QuoteRejected;
use IP\Modules\MailQueue\Support\MailQueue;
use IP\Support\Parser;

class QuoteRejectedListener
{
    public function __construct(MailQueue $mailQueue)
    {
        $this->mailQueue = $mailQueue;
    }

    public function handle(QuoteRejected $event)
    {
        $event->quote->activities()->create(['activity' => 'public.rejected']);

        $parser = new Parser($event->quote);

        $mail = $this->mailQueue->create($event->quote, [
            'to' => [$event->quote->user->email],
            'cc' => [config('fi.mailDefaultCc')],
            'bcc' => [config('fi.mailDefaultBcc')],
            'subject' => trans('ip.quote_status_change_notification'),
            'body' => $parser->parse('quoteRejectedEmailBody'),
            'attach_pdf' => config('fi.attachPdf'),
        ]);

        $this->mailQueue->send($mail->id);
    }
}
