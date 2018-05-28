<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AmountFieldDecimals extends Migration
{
    public function up()
    {
        Schema::table('invoice_amounts', function (Blueprint $table)
        {
            $table->decimal('subtotal', 20, 4)->change();
            $table->decimal('discount', 20, 4)->change();
            $table->decimal('tax', 20, 4)->change();
            $table->decimal('total', 20, 4)->change();
            $table->decimal('paid', 20, 4)->change();
            $table->decimal('balance', 20, 4)->change();
        });

        Schema::table('invoice_items', function (Blueprint $table)
        {
            $table->decimal('quantity', 20, 4)->change();
            $table->decimal('price', 20, 4)->change();
        });

        Schema::table('invoice_item_amounts', function (Blueprint $table)
        {
            $table->decimal('subtotal', 20, 4)->change();
            $table->decimal('tax_1', 20, 4)->change();
            $table->decimal('tax_2', 20, 4)->change();
            $table->decimal('tax', 20, 4)->change();
            $table->decimal('total', 20, 4)->change();
        });

        Schema::table('item_lookups', function (Blueprint $table)
        {
            $table->decimal('price', 20, 4)->change();
        });

        Schema::table('payments', function (Blueprint $table)
        {
            $table->decimal('amount', 20, 4)->change();
        });

        Schema::table('quote_amounts', function (Blueprint $table)
        {
            $table->decimal('subtotal', 20, 4)->change();
            $table->decimal('discount', 20, 4)->change();
            $table->decimal('tax', 20, 4)->change();
            $table->decimal('total', 20, 4)->change();
        });

        Schema::table('quote_items', function (Blueprint $table)
        {
            $table->decimal('quantity', 20, 4)->change();
            $table->decimal('price', 20, 4)->change();
        });

        Schema::table('quote_item_amounts', function (Blueprint $table)
        {
            $table->decimal('subtotal', 20, 4)->change();
            $table->decimal('tax_1', 20, 4)->change();
            $table->decimal('tax_2', 20, 4)->change();
            $table->decimal('tax', 20, 4)->change();
            $table->decimal('total', 20, 4)->change();
        });
    }

    public function down()
    {
        //
    }
}
