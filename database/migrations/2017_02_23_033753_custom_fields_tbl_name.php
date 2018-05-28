<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CustomFieldsTblName extends Migration
{
    public function up()
    {
        Schema::table('custom_fields', function (Blueprint $table)
        {
            $table->renameColumn('table_name', 'tbl_name');
        });
    }

    public function down()
    {
        //
    }
}
