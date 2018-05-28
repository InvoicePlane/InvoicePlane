<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ClientRemoveTemplates extends Migration
{
    public function up()
    {
        Schema::table('clients', function(Blueprint $table)
        {
            $table->dropColumn(['invoice_template', 'quote_template']);
        });
    }

    public function down()
    {
        //
    }
}
