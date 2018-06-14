<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class PaymentWithoutBalance extends Migration
{
    public function up()
    {
        Setting::saveByKey('allowPaymentsWithoutBalance', 0);
    }

    public function down()
    {
        //
    }
}
