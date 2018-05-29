<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class Notes extends Migration
{
    public function up()
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id');
            $table->integer('notable_id');
            $table->string('notable_type');
            $table->longText('note');
            $table->boolean('private');
        });
    }

    public function down()
    {
        //
    }
}
