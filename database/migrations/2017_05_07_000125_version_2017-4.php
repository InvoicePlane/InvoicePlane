<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Version20174 extends Migration
{
    public function up()
    {
        deleteTempFiles();
        deleteViewCache();

        Setting::saveByKey('version', '2017-4');
    }

    public function down()
    {
        //
    }
}
