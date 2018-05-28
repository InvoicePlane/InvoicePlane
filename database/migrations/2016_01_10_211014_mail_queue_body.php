<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MailQueueBody extends Migration
{
    public function up()
    {
        Schema::table('mail_queue', function (Blueprint $table)
        {
            $table->longText('body')->change();
        });
    }

    public function down()
    {
        //
    }
}
