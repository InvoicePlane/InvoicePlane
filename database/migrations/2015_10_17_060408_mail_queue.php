<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MailQueue extends Migration
{
    public function up()
    {
        Schema::create('mail_queue', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('mailable_id');
            $table->string('mailable_type');
            $table->string('from');
            $table->string('to');
            $table->string('cc');
            $table->string('bcc');
            $table->string('subject');
            $table->string('body');
            $table->boolean('attach_pdf');
            $table->boolean('sent');
            $table->text('error')->nullable();
        });
    }

    public function down()
    {
        //
    }
}
