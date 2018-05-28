<?php

use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\CustomFields\Models\CompanyProfileCustom;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CompanyProfileCustomFields extends Migration
{
    public function up()
    {
        Schema::create('company_profiles_custom', function (Blueprint $table)
        {
            $table->integer('company_profile_id');
            $table->timestamps();

            $table->primary('company_profile_id');
        });

        foreach (CompanyProfile::get() as $companyProfile)
        {
            $companyProfile->custom()->save(new CompanyProfileCustom());
        }
    }

    public function down()
    {
        //
    }
}
