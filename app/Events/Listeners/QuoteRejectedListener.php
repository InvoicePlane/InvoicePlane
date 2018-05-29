<?php

namespace FI\Events\Listeners;

use FI\Events\QuoteRejected;
use FI\Modules\MailQueue\Support\MailQueue;
use FI\Support\Parser;

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
            'subject' => trans('fi.quote_status_change_notification'),
            'body' => $parser->parse('quoteRejectedEmailBody'),
            'attach_pdf' => config('fi.attachPdf'),
        ]);

        $this->mailQueue->send($mail->id);
    }
}
