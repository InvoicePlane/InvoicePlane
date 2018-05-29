<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ItemTaxes extends Migration
{
    public function up()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->integer('tax_rate_2_id')->after('tax_rate_id')->default(0);

            $table->index('tax_rate_2_id');
        });

        Schema::table('invoice_item_amounts', function (Blueprint $table) {
            $table->decimal('tax_2', 15, 2)->default(0.00)->after('subtotal');
            $table->decimal('tax_1', 15, 2)->default(0.00)->after('subtotal');
        });

        Schema::table('quote_items', function (Blueprint $table) {
            $table->integer('tax_rate_2_id')->after('tax_rate_id')->default(0);

            $table->index('tax_rate_2_id');
        });

        Schema::table('quote_item_amounts', function (Blueprint $table) {
            $table->decimal('tax_2', 15, 2)->default(0.00)->after('subtotal');
            $table->decimal('tax_1', 15, 2)->default(0.00)->after('subtotal');
        });

        Schema::table('tax_rates', function (Blueprint $table) {
            $table->boolean('is_compound')->default(0);
        });
    }

    public function down()
    {
        //
    }
}
