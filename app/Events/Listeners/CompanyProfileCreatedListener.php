<?php

namespace IP\Events\Listeners;

use IP\Events\CompanyProfileCreated;
use IP\Modules\CustomFields\Models\CompanyProfileCustom;

class CompanyProfileCreatedListener
{
    public function __construct()
    {
        //
    }

    public function handle(CompanyProfileCreated $event)
    {
        // Create the default custom record.
        $event->companyProfile->custom()->save(new CompanyProfileCustom());
    }
}
