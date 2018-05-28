<?php

use FI\Modules\Activity\Models\Activity;
use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Version223 extends Migration
{
    public function up()
    {
        // Delete invalid quote activity records if they exist
        Activity::where('audit_type', 'FI\Modules\Quotes\Models\Quote')
            ->whereNotIn('audit_id', function ($query)
            {
                $query->select('id')->from('quotes');
            })->delete();

        // Delete invalid invoice activity records if they exist
        Activity::where('audit_type', 'FI\Modules\Invoices\Models\Invoice')
            ->whereNotIn('audit_id', function ($query)
            {
                $query->select('id')->from('invoices');
            })->delete();

        Setting::saveByKey('version', '2.2.3');
    }

    public function down()
    {
        //
    }
}
