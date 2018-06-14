<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class QuoteStatusChangeSettings extends Migration
{
    public function up()
    {
        Setting::saveByKey('quoteApprovedEmailBody', '<p><a href="{{ $quote->public_url }}">Quote #{{ $quote->number }}</a> has been APPROVED.</p>');
        Setting::saveByKey('quoteRejectedEmailBody', '<p><a href="{{ $quote->public_url }}">Quote #{{ $quote->number }}</a> has been REJECTED.</p>');
    }

    public function down()
    {
        //
    }
}
