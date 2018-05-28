<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CompanyProfilesDelCols extends Migration
{
    public function up()
    {
        Schema::table('users', function(Blueprint $table)
        {
            $table->dropColumn(['company', 'address', 'city', 'state', 'zip', 'country', 'phone', 'fax', 'mobile', 'web']);
        });
    }

    public function down()
    {
        //
    }
}
