<?php

use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CompanyProfileLogoTemplates extends Migration
{
    public function up()
    {
        Schema::table('company_profiles', function (Blueprint $table)
        {
            $table->string('logo')->nullable();
            $table->string('quote_template');
            $table->string('invoice_template');
        });

        CompanyProfile::where('invoice_template', '')->update(['invoice_template' => Setting::getByKey('invoiceTemplate')]);
        CompanyProfile::where('quote_template', '')->update(['quote_template' => Setting::getByKey('quoteTemplate')]);
        CompanyProfile::where('logo', null)->update(['logo' => Setting::getByKey('logo')]);

        Setting::deleteByKey('logo');
    }

    public function down()
    {
        //
    }
}
