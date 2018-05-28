<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QuoteCreateDate extends Migration
{
    public function up()
    {
        Schema::table('quotes', function (Blueprint $table)
        {
            $table->date('quote_date')->after('updated_at');
        });

        DB::table('quotes')->update(['quote_date' => DB::raw('created_at')]);
    }

    public function down()
    {
        //
    }
}
