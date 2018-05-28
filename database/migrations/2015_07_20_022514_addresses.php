<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class Addresses extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table)
        {
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('zip')->nullable()->after('state');
            $table->string('country')->nullable()->after('zip');
        });
    }

    public function down()
    {
        //
    }
}
