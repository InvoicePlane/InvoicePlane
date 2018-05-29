<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class Discount extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('discount', 15, 2)->default(0.00);
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->decimal('discount', 15, 2)->default(0.00);
        });
    }

    public function down()
    {
        //
    }
}
