<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UserCustomFields extends Migration
{
    public function up()
    {
        Schema::create('users_custom', function (Blueprint $table) {
            $table->integer('user_id');
            $table->timestamps();

            $table->primary('user_id');
        });
    }

    public function down()
    {
        //
    }
}
