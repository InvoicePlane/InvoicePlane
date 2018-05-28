<?php

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class ProfileImageSetting extends Migration
{
    public function up()
    {
        Setting::saveByKey('displayProfileImage', '1');
    }

    public function down()
    {
        //
    }
}
