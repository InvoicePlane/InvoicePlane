<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecurringInvoicesCustom extends Migration
{
    public function up()
    {
        Schema::create('recurring_invoices_custom', function (Blueprint $table)
        {
            $table->integer('recurring_invoice_id');
            $table->timestamps();

            $table->primary('recurring_invoice_id');
        });
    }

    public function down()
    {
        //
    }
}
