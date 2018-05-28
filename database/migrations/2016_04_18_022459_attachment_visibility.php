<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AttachmentVisibility extends Migration
{
    public function up()
    {
        Schema::table('attachments', function (Blueprint $table)
        {
            $table->integer('client_visibility');
        });
    }

    public function down()
    {
        //
    }
}
