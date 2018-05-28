<?php

use FI\Modules\Users\Models\User;
use Illuminate\Database\Migrations\Migration;

class ClientUserAccounts extends Migration
{
    public function up()
    {
        User::whereHas('client', function ($client)
        {
            $client->where('allow_login', 0);
        })->delete();

        User::where('client_id', '<>', 0)->whereNotIn('client_id', function ($client)
        {
            $client->select('id')->from('clients');
        })->delete();
    }

    public function down()
    {
        //
    }
}
