<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class InvoiceQuoteSummary extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table)
        {
            $table->string('summary', 100)->nullable();
        });

        Schema::table('quotes', function (Blueprint $table)
        {
            $table->string('summary', 100)->nullable();
        });
    }

    public function down()
    {
        //
    }
}
