<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class StatusFilters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::saveByKey('invoiceStatusFilter', 'all_statuses');
        Setting::saveByKey('quoteStatusFilter', 'all_statuses');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
