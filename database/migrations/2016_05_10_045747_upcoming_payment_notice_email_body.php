<?php

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class UpcomingPaymentNoticeEmailBody extends Migration
{
    public function up()
    {
        Setting::saveByKey('upcomingPaymentNoticeEmailBody', '<p>This is a notice to let you know your invoice from {{ $invoice->user->name }} for {{ $invoice->amount->formatted_total }} is due on {{ $invoice->formatted_due_at }}. Click the link below to view the invoice:</p>' . "\r\n\r\n" . '<p><a href="{{ $invoice->public_url }}">{{ $invoice->public_url }}</a></p>');
    }

    public function down()
    {
        //
    }
}
