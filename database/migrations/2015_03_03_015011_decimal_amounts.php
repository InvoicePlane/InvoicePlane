<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DecimalAmounts extends Migration
{
    public function up()
    {
        Schema::table('invoice_amounts', function (Blueprint $table) {
            $table->renameColumn('item_subtotal', 'old_item_subtotal');
        });

        Schema::table('invoice_amounts', function (Blueprint $table) {
            $table->renameColumn('item_tax_total', 'old_item_tax_total');
        });

        Schema::table('invoice_amounts', function (Blueprint $table) {
            $table->renameColumn('tax_total', 'old_tax_total');
        });

        Schema::table('invoice_amounts', function (Blueprint $table) {
            $table->renameColumn('total', 'old_total');
        });

        Schema::table('invoice_amounts', function (Blueprint $table) {
            $table->renameColumn('paid', 'old_paid');
        });

        Schema::table('invoice_amounts', function (Blueprint $table) {
            $table->renameColumn('balance', 'old_balance');

        });

        Schema::table('invoice_amounts', function (Blueprint $table) {
            $table->decimal('item_subtotal', 15, 2)->default(0.00);
            $table->decimal('item_tax_total', 15, 2)->default(0.00);
            $table->decimal('tax_total', 15, 2)->default(0.00);
            $table->decimal('total', 15, 2)->default(0.00);
            $table->decimal('paid', 15, 2)->default(0.00);
            $table->decimal('balance', 15, 2)->default(0.00);
        });

        DB::table('invoice_amounts')->update([
            'item_subtotal' => DB::raw('old_item_subtotal'),
            'item_tax_total' => DB::raw('old_item_tax_total'),
            'tax_total' => DB::raw('old_tax_total'),
            'total' => DB::raw('old_total'),
            'paid' => DB::raw('old_paid'),
            'balance' => DB::raw('old_balance'),
        ]);

        Schema::table('invoice_amounts', function (Blueprint $table) {
            $table->dropColumn(['old_item_subtotal', 'old_item_tax_total', 'old_tax_total', 'old_total', 'old_paid', 'old_balance']);
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->renameColumn('price', 'old_price');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->default(0.00);
        });

        DB::table('invoice_items')->update(['price' => DB::raw('old_price')]);

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('old_price');
        });

        Schema::table('invoice_item_amounts', function (Blueprint $table) {
            $table->renameColumn('subtotal', 'old_subtotal');
        });

        Schema::table('invoice_item_amounts', function (Blueprint $table) {
            $table->renameColumn('tax_total', 'old_tax_total');
        });

        Schema::table('invoice_item_amounts', function (Blueprint $table) {
            $table->renameColumn('total', 'old_total');
        });

        Schema::table('invoice_item_amounts', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->decimal('tax_total', 15, 2)->default(0.00);
            $table->decimal('total', 15, 2)->default(0.00);
        });

        DB::table('invoice_item_amounts')->update([
            'subtotal' => DB::raw('old_subtotal'),
            'tax_total' => DB::raw('old_tax_total'),
            'total' => DB::raw('old_total'),
        ]);

        Schema::table('invoice_item_amounts', function (Blueprint $table) {
            $table->dropColumn(['old_subtotal', 'old_tax_total', 'old_total']);
        });

        Schema::table('invoice_tax_rates', function (Blueprint $table) {
            $table->renameColumn('tax_total', 'old_tax_total');
        });

        Schema::table('invoice_tax_rates', function (Blueprint $table) {
            $table->decimal('tax_total', 15, 2)->default(0.00);
        });

        DB::table('invoice_tax_rates')->update(['tax_total' => DB::raw('old_tax_total')]);

        Schema::table('invoice_tax_rates', function (Blueprint $table) {
            $table->dropColumn('old_tax_total');
        });

        Schema::table('item_lookups', function (Blueprint $table) {
            $table->renameColumn('price', 'old_price');
        });

        Schema::table('item_lookups', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->default(0.00);
        });

        DB::table('item_lookups')->update(['price' => DB::raw('old_price')]);

        Schema::table('item_lookups', function (Blueprint $table) {
            $table->dropColumn('old_price');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('amount', 'old_amount');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->default(0.00);
        });

        DB::table('payments')->update(['amount' => DB::raw('old_amount')]);

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('old_amount');
        });

        Schema::table('quote_amounts', function (Blueprint $table) {
            $table->renameColumn('item_subtotal', 'old_item_subtotal');
        });

        Schema::table('quote_amounts', function (Blueprint $table) {
            $table->renameColumn('item_tax_total', 'old_item_tax_total');
        });

        Schema::table('quote_amounts', function (Blueprint $table) {
            $table->renameColumn('tax_total', 'old_tax_total');
        });

        Schema::table('quote_amounts', function (Blueprint $table) {
            $table->renameColumn('total', 'old_total');
        });

        Schema::table('quote_amounts', function (Blueprint $table) {
            $table->decimal('item_subtotal', 15, 2)->default(0.00);
            $table->decimal('item_tax_total', 15, 2)->default(0.00);
            $table->decimal('tax_total', 15, 2)->default(0.00);
            $table->decimal('total', 15, 2)->default(0.00);
        });

        DB::table('quote_amounts')->update([
            'item_subtotal' => DB::raw('old_item_subtotal'),
            'item_tax_total' => DB::raw('old_item_tax_total'),
            'tax_total' => DB::raw('old_tax_total'),
            'total' => DB::raw('old_total'),
        ]);

        Schema::table('quote_amounts', function (Blueprint $table) {
            $table->dropColumn(['old_item_subtotal', 'old_item_tax_total', 'old_tax_total', 'old_total']);
        });

        Schema::table('quote_items', function (Blueprint $table) {
            $table->renameColumn('price', 'old_price');
        });

        Schema::table('quote_items', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->default(0.00);
        });

        DB::table('quote_items')->update(['price' => DB::raw('old_price')]);

        Schema::table('quote_items', function (Blueprint $table) {
            $table->dropColumn('old_price');
        });

        Schema::table('quote_item_amounts', function (Blueprint $table) {
            $table->renameColumn('subtotal', 'old_subtotal');
        });

        Schema::table('quote_item_amounts', function (Blueprint $table) {
            $table->renameColumn('tax_total', 'old_tax_total');
        });

        Schema::table('quote_item_amounts', function (Blueprint $table) {
            $table->renameColumn('total', 'old_total');
        });

        Schema::table('quote_item_amounts', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->decimal('tax_total', 15, 2)->default(0.00);
            $table->decimal('total', 15, 2)->default(0.00);
        });

        DB::table('quote_item_amounts')->update([
            'subtotal' => DB::raw('old_subtotal'),
            'tax_total' => DB::raw('old_tax_total'),
            'total' => DB::raw('old_total'),
        ]);

        Schema::table('quote_item_amounts', function (Blueprint $table) {
            $table->dropColumn(['old_subtotal', 'old_tax_total', 'old_total']);
        });

        Schema::table('quote_tax_rates', function (Blueprint $table) {
            $table->renameColumn('tax_total', 'old_tax_total');
        });

        Schema::table('quote_tax_rates', function (Blueprint $table) {
            $table->decimal('tax_total', 15, 2)->default(0.00);
        });

        DB::table('quote_tax_rates')->update(['tax_total' => DB::raw('old_tax_total')]);

        Schema::table('quote_tax_rates', function (Blueprint $table) {
            $table->dropColumn('old_tax_total');
        });
    }

    public function down()
    {
        //
    }
}
