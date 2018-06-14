<?php

namespace IP\Events;

use IP\Modules\Invoices\Models\Invoice;
use IP\Modules\MailQueue\Models\MailQueue;
use Illuminate\Queue\SerializesModels;

class OverdueNoticeEmailed extends Event
{
    use SerializesModels;

    public function __construct(Invoice $invoice, MailQueue $mail)
    {
        $this->invoice = $invoice;
        $this->mail = $mail;
    }

    public function broadcastOn()
    {
        return [];
    }
}
