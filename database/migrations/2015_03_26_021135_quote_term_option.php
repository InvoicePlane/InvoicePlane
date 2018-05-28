<?php

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class QuoteTermOption extends Migration
{
    public function up()
    {
        Setting::saveByKey('convertQuoteTerms', 'quote');
    }

    public function down()
    {
        //
    }
}
