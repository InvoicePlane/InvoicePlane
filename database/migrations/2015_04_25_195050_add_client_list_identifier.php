<?php

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddClientListIdentifier extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table)
        {
            $table->string('unique_name')->nullable();

            $table->index('unique_name');
        });

        DB::table('clients')->update(['unique_name' => DB::raw('name')]);

        Setting::saveByKey('displayClientUniqueName', '0');
    }

    public function down()
    {
        //
    }
}