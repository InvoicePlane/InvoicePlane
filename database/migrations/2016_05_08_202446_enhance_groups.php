<?php

use IP\Modules\Groups\Models\Group;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class EnhanceGroups extends Migration
{
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->string('format');
            $table->integer('reset_number');
        });

        $groups = Group::all();

        foreach ($groups as $group) {
            $format = '';

            if ($group->prefix) $format .= $group->prefix;
            if ($group->prefix_year) $format .= '{YEAR}';
            if ($group->prefix_month) $format .= '{MONTH}';

            $format .= '{NUMBER}';

            $group->format = $format;

            $group->save();
        }

        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn(['prefix', 'prefix_year', 'prefix_month']);
        });
    }

    public function down()
    {
        //
    }
}
