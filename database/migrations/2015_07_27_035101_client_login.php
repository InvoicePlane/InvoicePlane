<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ClientLogin extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table)
        {
            $table->boolean('allow_login')->default(0);
        });

        Schema::table('users', function (Blueprint $table)
        {
            $table->integer('client_id');

            $table->index('client_id');
        });
    }

    public function down()
    {
        //
    }
}
