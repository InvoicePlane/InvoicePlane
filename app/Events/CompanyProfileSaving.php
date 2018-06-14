<?php

namespace IP\Events;

use IP\Modules\CompanyProfiles\Models\CompanyProfile;
use Illuminate\Queue\SerializesModels;

class CompanyProfileSaving extends Event
{
    use SerializesModels;

    public function __construct(CompanyProfile $companyProfile)
    {
        $this->companyProfile = $companyProfile;
    }
}