<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class QuoteTerms extends Migration
{
    public function up()
    {
        Schema::table('quotes', function (Blueprint $table)
        {
            $table->text('terms')->nullable();
        });
    }

    public function down()
    {
        //
    }
}
