<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class PaymentReceipt extends Migration
{
    public function up()
    {
        Setting::saveByKey('paymentReceiptBody', '<p>Thank you! Your payment of {{ $payment->formatted_amount }} has been applied to Invoice #{{ $payment->invoice->number }}.</p>');
    }

    public function down()
    {
        //
    }
}
