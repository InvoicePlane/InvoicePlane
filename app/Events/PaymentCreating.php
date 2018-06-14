<?php

namespace IP\Events;

use IP\Modules\Payments\Models\Payment;
use Illuminate\Queue\SerializesModels;

class PaymentCreating extends Event
{
    use SerializesModels;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }
}
