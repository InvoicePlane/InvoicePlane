<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class FixTemplateFields extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table)
        {
            $table->renameColumn('invoice_template', 'old_invoice_template');
        });

        Schema::table('clients', function (Blueprint $table)
        {
            $table->renameColumn('quote_template', 'old_quote_template');
        });

        Schema::table('clients', function (Blueprint $table)
        {
            $table->string('invoice_template')->nullable();
            $table->string('quote_template')->nullable();
        });

        Schema::table('invoices', function (Blueprint $table)
        {
            $table->renameColumn('template', 'old_template');
        });

        Schema::table('invoices', function (Blueprint $table)
        {
            $table->string('template')->nullable();
        });

        Schema::table('quotes', function (Blueprint $table)
        {
            $table->renameColumn('template', 'old_template');
        });

        Schema::table('quotes', function (Blueprint $table)
        {
            $table->string('template')->nullable();
        });

        DB::table('clients')->update(['invoice_template' => DB::raw('old_invoice_template')]);
        DB::table('clients')->update(['quote_template' => DB::raw('old_quote_template')]);
        DB::table('invoices')->update(['template' => DB::raw('old_template')]);
        DB::table('quotes')->update(['template' => DB::raw('old_template')]);

        Schema::table('clients', function (Blueprint $table)
        {
            $table->dropColumn('old_invoice_template');
        });

        Schema::table('clients', function (Blueprint $table)
        {
            $table->dropColumn('old_quote_template');
        });

        Schema::table('invoices', function (Blueprint $table)
        {
            $table->dropColumn('old_template');
        });

        Schema::table('quotes', function (Blueprint $table)
        {
            $table->dropColumn('old_template');
        });
    }

    public function down()
    {
        //
    }
}
