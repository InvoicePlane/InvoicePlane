<?php

namespace IP\Events\Listeners;

use IP\Events\CompanyProfileDeleted;

class CompanyProfileDeletedListener
{
    public function __construct()
    {
        //
    }

    public function handle(CompanyProfileDeleted $event)
    {
        $event->companyProfile->custom->delete();
    }
}
