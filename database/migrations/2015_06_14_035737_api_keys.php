<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ApiKeys extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table)
        {
            $table->string('api_public_key')->nullable();
            $table->string('api_secret_key')->nullable();
        });
    }

    public function down()
    {
        //
    }
}
