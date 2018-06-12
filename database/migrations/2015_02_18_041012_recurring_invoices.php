<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RecurringInvoices extends Migration
{
    public function up()
    {
        Schema::table('recurring_invoices', function (Blueprint $table) {
            $table->renameColumn('generate_at', 'old_generate_at');
        });

        Schema::table('recurring_invoices', function (Blueprint $table) {
            $table->date('generate_at')->nullable();
            $table->date('stop_at')->nullable();
        });

        DB::table('recurring_invoices')->update(['generate_at' => DB::raw('old_generate_at')]);

        Schema::table('recurring_invoices', function (Blueprint $table) {
            $table->dropColumn('old_generate_at');
        });
    }

    public function down()
    {
        //
    }
}
