<?php

/**
 * InvoicePlane
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (C) 2014 - 2018 InvoicePlane
 * @license     https://invoiceplane.com/license
 * @link        https://invoiceplane.com
 *
 * Based on FusionInvoice by Jesse Terry (FusionInvoice, LLC)
 */

namespace IP\Modules\CompanyProfiles\Controllers;

use IP\Http\Controllers\Controller;
use IP\Modules\CompanyProfiles\Models\CompanyProfile;

class LogoController extends Controller
{
    public function logo($id)
    {
        $companyProfile = CompanyProfile::find($id);

        if ($companyProfile->logo) {
            return response(file_get_contents(storage_path($companyProfile->logo)), 200)->header('Content-Type',
                'image/jpeg');
        }

        return null;
    }
}
