<?php

use FI\Modules\Clients\Models\Client;
use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ClientLanguage extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('language')->nullable();
        });

        Client::where('language', null)->update(['language' => Setting::getByKey('language')]);
    }

    public function down()
    {
        //
    }
}
