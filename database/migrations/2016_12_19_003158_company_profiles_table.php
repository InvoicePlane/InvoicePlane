<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CompanyProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('company_profiles', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->string('company')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('mobile')->nullable();
            $table->string('web')->nullable();
        });
    }

    public function down()
    {
        //
    }
}
