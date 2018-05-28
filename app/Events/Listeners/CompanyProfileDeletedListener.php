<?php

namespace FI\Events\Listeners;

use FI\Events\CompanyProfileDeleted;

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
