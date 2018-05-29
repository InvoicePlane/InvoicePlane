<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UserUpdate extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('remember_token', 100)->nullable();
        });
    }

    public function down()
    {
        //
    }
}
