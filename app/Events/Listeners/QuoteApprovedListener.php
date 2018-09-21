<?php

namespace IP\Events\Listeners;

use IP\Events\QuoteApproved;
use IP\Modules\MailQueue\Support\MailQueue;
use IP\Modules\Quotes\Support\QuoteToInvoice;
use IP\Support\DateFormatter;
use IP\Support\Parser;

class QuoteApprovedListener
{
    public function __construct(MailQueue $mailQueue, QuoteToInvoice $quoteToInvoice)
    {
        $this->mailQueue = $mailQueue;
        $this->quoteToInvoice = $quoteToInvoice;
    }

    public function handle(QuoteApproved $event)
    {
        // Create the activity record
        $event->quote->activities()->create(['activity' => 'public.approved']);

        // If applicable, convert the quote to an invoice when quote is approved
        if (config('ip.convertQuoteWhenApproved')) {
            $this->quoteToInvoice->convert(
                $event->quote,
                date('Y-m-d'),
                DateFormatter::incrementDateByDays(date('Y-m-d'), config('ip.invoicesDueAfter')),
                config('ip.invoiceGroup')
            );
        }

        $parser = new Parser($event->quote);

        $mail = $this->mailQueue->create($event->quote, [
            'to' => [$event->quote->user->email],
            'cc' => [config('ip.mailDefaultCc')],
            'bcc' => [config('ip.mailDefaultBcc')],
            'subject' => trans('ip.quote_status_change_notification'),
            'body' => $parser->parse('quoteApprovedEmailBody'),
            'attach_pdf' => config('ip.attachPdf'),
        ]);

        $this->mailQueue->send($mail->id);
    }
}
