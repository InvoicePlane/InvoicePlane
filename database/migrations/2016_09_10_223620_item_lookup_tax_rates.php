<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ItemLookupTaxRates extends Migration
{
    public function up()
    {
        Schema::table('item_lookups', function (Blueprint $table)
        {
            $table->integer('tax_rate_id')->default(0);
            $table->integer('tax_rate_2_id')->default(0);

            $table->index('tax_rate_id');
            $table->index('tax_rate_2_id');
        });
    }

    public function down()
    {
        //
    }
}
