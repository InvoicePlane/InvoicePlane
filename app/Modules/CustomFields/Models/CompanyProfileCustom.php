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

namespace IP\Modules\CustomFields\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProfileCustom extends Model
{
    protected $table = 'company_profiles_custom';

    protected $primaryKey = 'company_profile_id';

    protected $guarded = [];
}