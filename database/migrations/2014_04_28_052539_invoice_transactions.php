<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class InvoiceTransactions extends Migration
{
    public function up()
    {
        Schema::create('invoice_transactions', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->integer('invoice_id');
            $table->boolean('is_successful');
            $table->string('transaction_reference')->nullable();
        });
    }

    public function down()
    {
        //
    }
}
