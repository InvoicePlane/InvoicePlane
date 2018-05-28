<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class Activities extends Migration
{
    public function up()
    {
        Schema::create('activities', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->string('object');
            $table->string('activity');
            $table->integer('parent_id');
            $table->text('info')->nullable();

            $table->index('object');
            $table->index('activity');
            $table->index('parent_id');
        });
    }

    public function down()
    {
        //
    }
}
