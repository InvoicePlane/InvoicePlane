<?php

use FI\Modules\MailQueue\Models\MailQueue;
use Illuminate\Database\Migrations\Migration;

class MailQueueFixPaymentNotice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        MailQueue::where('to', 'like', '{%')
            ->orWhere('cc', 'like', '{%')
            ->orWhere('bcc', 'like', '{%')
            ->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
