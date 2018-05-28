<?php

namespace FI\Events;

use FI\Modules\Payments\Models\Payment;
use Illuminate\Queue\SerializesModels;

class PaymentCreating extends Event
{
    use SerializesModels;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }
}
