<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CompanyProfilesFks extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table)
        {
            $table->integer('company_profile_id');

            $table->index('company_profile_id');
        });

        Schema::table('quotes', function (Blueprint $table)
        {
            $table->integer('company_profile_id');

            $table->index('company_profile_id');
        });

        Schema::table('expenses', function (Blueprint $table)
        {
            $table->integer('company_profile_id');

            $table->index('company_profile_id');
        });
    }

    public function down()
    {
        //
    }
}
