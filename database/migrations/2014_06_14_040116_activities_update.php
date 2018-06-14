<?php

use IP\Modules\Activity\Models\Activity;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ActivitiesUpdate extends Migration
{
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->renameColumn('object', 'audit_type');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->renameColumn('parent_id', 'audit_id');
        });

        Activity::where('audit_type', 'quote')->update(['audit_type' => 'IP\Modules\Quotes\Models\Quote']);
        Activity::where('audit_type', 'invoice')->update(['audit_type' => 'IP\Modules\Invoices\Models\Invoice']);
    }

    public function down()
    {
        //
    }
}
