<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class TaxRateDecimals extends Migration
{
    public function up()
    {
        Schema::table('tax_rates', function (Blueprint $table)
        {
            $table->renameColumn('percent', 'old_percent');
        });

        Schema::table('tax_rates', function (Blueprint $table)
        {
            $table->decimal('percent', 5, 3)->default(0.00);
        });

        DB::table('tax_rates')->update(['percent' => DB::raw('old_percent')]);

        Schema::table('tax_rates', function (Blueprint $table)
        {
            $table->dropColumn('old_percent');
        });
    }

    public function down()
    {
        //
    }
}
