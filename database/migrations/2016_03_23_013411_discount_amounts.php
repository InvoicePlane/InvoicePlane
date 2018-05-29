<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DiscountAmounts extends Migration
{
    public function up()
    {
        Schema::table('invoice_amounts', function (Blueprint $table) {
            $table->decimal('discount', 15, 2)->default(0.00)->after('subtotal');
        });

        Schema::table('quote_amounts', function (Blueprint $table) {
            $table->decimal('discount', 15, 2)->default(0.00)->after('subtotal');
        });
    }

    public function down()
    {
        //
    }
}
