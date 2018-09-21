<?php

namespace IP\Events\Listeners;

use IP\Events\InvoiceCreatedRecurring;
use IP\Events\InvoiceEmailed;
use IP\Modules\MailQueue\Support\MailQueue;
use IP\Support\Parser;

class InvoiceCreatedRecurringListener
{
    public function __construct(MailQueue $mailQueue)
    {
        $this->mailQueue = $mailQueue;
    }

    public function handle(InvoiceCreatedRecurring $event)
    {
        if (config('ip.automaticEmailOnRecur') and $event->invoice->client->email) {
            $parser = new Parser($event->invoice);

            if (!$event->invoice->is_overdue) {
                $subject = $parser->parse('invoiceEmailSubject');
                $body = $parser->parse('invoiceEmailBody');
            } else {
                $subject = $parser->parse('overdueInvoiceEmailSubject');
                $body = $parser->parse('overdueInvoiceEmailBody');
            }

            $mail = $this->mailQueue->create($event->invoice, [
                'to' => [$event->invoice->client->email],
                'cc' => [config('ip.mailDefaultCc')],
                'bcc' => [config('ip.mailDefaultBcc')],
                'subject' => $subject,
                'body' => $body,
                'attach_pdf' => config('ip.attachPdf'),
            ]);

            $this->mailQueue->send($mail->id);

            event(new InvoiceEmailed($event->invoice));
        }
    }
}
