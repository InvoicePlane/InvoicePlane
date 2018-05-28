<?php

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class EmailSubjectSettings extends Migration
{
    public function up()
    {
        Setting::saveByKey('quoteEmailSubject', 'Quote #{{ $quote->number }}');
        Setting::saveByKey('invoiceEmailSubject', 'Invoice #{{ $invoice->number }}');
        Setting::saveByKey('overdueInvoiceEmailSubject', 'Overdue Invoice Reminder: Invoice #{{ $invoice->number }}');
        Setting::saveByKey('upcomingPaymentNoticeEmailSubject', 'Upcoming Payment Due Notice: Invoice #{{ $invoice->number }}');
        Setting::saveByKey('paymentReceiptEmailSubject', 'Payment Receipt for Invoice #{{ $payment->invoice->number }}');
    }

    public function down()
    {
        //
    }
}
