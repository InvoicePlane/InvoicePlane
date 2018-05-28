<?php

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Version220 extends Migration
{
    public function up()
    {
        Setting::saveByKey('overdueInvoiceEmailBody', '<p>This is a reminder to let you know your invoice from {{ $invoice->user->name }} for {{ $invoice->amount->formatted_total }} is overdue. Click the link below to view the invoice:</p>' . "\r\n\r\n" . '<p><a href="{{ $invoice->public_url }}">{{ $invoice->public_url }}</a></p>');
        Setting::saveByKey('invoiceEmailBody', '<p>To view your invoice from {{ $invoice->user->name }} for {{ $invoice->amount->formatted_total }}, click the link below:</p>' . "\r\n\r\n" . '<p><a href="{{ $invoice->public_url }}">{{ $invoice->public_url }}</a></p>');
        Setting::saveByKey('quoteEmailBody', '<p>To view your quote from {{ $quote->user->name }} for {{ $quote->amount->formatted_total }}, click the link below:</p>' . "\r\n\r\n" . '<p><a href="{{ $quote->public_url }}">{{ $quote->public_url }}</a></p>');
        Setting::saveByKey('convertQuoteWhenApproved', 1);
        Setting::saveByKey('paperOrientation', 'portrait');
        Setting::saveByKey('paperSize', 'letter');
        Setting::saveByKey('version', '2.2.0');
    }

    public function down()
    {
        //
    }
}
