<?php

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class ResultsPerPage extends Migration
{
    public function up()
    {
        Setting::saveByKey('resultsPerPage', 15);
    }

    public function down()
    {
        //
    }
}
