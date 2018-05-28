<?php

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Profileimagedriver extends Migration
{
    public function up()
    {
        Setting::saveByKey('profileImageDriver', 'Gravatar');
    }

    public function down()
    {
        //
    }
}
