<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class Addons extends Migration
{
    public function up()
    {
        Schema::create('addons', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->string('author_name');
            $table->string('author_url');
            $table->longText('navigation_menu')->nullable();
            $table->longText('system_menu')->nullable();
            $table->string('path');
            $table->boolean('enabled')->default(0);
        });
    }

    public function down()
    {
        //
    }
}
