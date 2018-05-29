<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class Amounts extends Migration
{
    public function up()
    {
        Schema::table('invoice_amounts', function (Blueprint $table) {
            $table->dropColumn('tax_total');
        });

        Schema::table('quote_amounts', function (Blueprint $table) {
            $table->dropColumn('tax_total');
        });

        Schema::table('invoice_amounts', function (Blueprint $table) {
            $table->renameColumn('item_subtotal', 'subtotal');
            $table->renameColumn('item_tax_total', 'tax');
        });

        Schema::table('quote_amounts', function (Blueprint $table) {
            $table->renameColumn('item_subtotal', 'subtotal');
            $table->renameColumn('item_tax_total', 'tax');
        });

        Schema::table('invoice_item_amounts', function (Blueprint $table) {
            $table->renameColumn('tax_total', 'tax');
        });

        Schema::table('quote_item_amounts', function (Blueprint $table) {
            $table->renameColumn('tax_total', 'tax');
        });
    }

    public function down()
    {
        //
    }
}
