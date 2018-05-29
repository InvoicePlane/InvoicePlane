<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddonReports extends Migration
{
    public function up()
    {
        Schema::table('addons', function (Blueprint $table) {
            $table->longText('navigation_reports')->nullable();
        });
    }

    public function down()
    {
        //
    }
}
