<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class IncreaseSummaryField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotes', function ($table) {
            $table->string('summary', 255)->change();
        });

        Schema::table('invoices', function ($table) {
            $table->string('summary', 255)->change();
        });

        Schema::table('recurring_invoices', function ($table) {
            $table->string('summary', 255)->change();
        });
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
