<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class FixExpenseColumn extends Migration
{
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
        });
    }

    public function down()
    {
        //
    }
}
