<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class GroupsLastGenerated extends Migration
{
    public function up()
    {
        Schema::table('groups', function (Blueprint $table)
        {
            $table->integer('last_id');
            $table->integer('last_year');
            $table->integer('last_month');
            $table->integer('last_week');
        });
    }

    public function down()
    {
        //
    }
}
