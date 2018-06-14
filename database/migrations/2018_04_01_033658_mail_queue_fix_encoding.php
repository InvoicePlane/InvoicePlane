<?php

use IP\Modules\MailQueue\Models\MailQueue;
use Illuminate\Database\Migrations\Migration;

class MailQueueFixEncoding extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (MailQueue::where('to', 'like', '"%')->get() as $record)
        {
            $record->to = json_encode([json_decode($record->to)]);
            $record->save();
        }

        foreach (MailQueue::where('cc', 'like', '"%')->get() as $record)
        {
            $record->cc = json_encode([json_decode($record->cc)]);
            $record->save();
        }

        foreach (MailQueue::where('bcc', 'like', '"%')->get() as $record)
        {
            $record->bcc = json_encode([json_decode($record->bcc)]);
            $record->save();
        }
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
