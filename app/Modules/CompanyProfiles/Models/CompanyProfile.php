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

namespace IP\Modules\CompanyProfiles\Models;

use IP\Events\CompanyProfileCreated;
use IP\Events\CompanyProfileCreating;
use IP\Events\CompanyProfileDeleted;
use IP\Events\CompanyProfileSaving;
use IP\Modules\Expenses\Models\Expense;
use IP\Modules\Invoices\Models\Invoice;
use IP\Modules\Quotes\Models\Quote;
use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($companyProfile) {
            event(new CompanyProfileSaving($companyProfile));
        });

        static::creating(function ($companyProfile) {
            event(new CompanyProfileCreating($companyProfile));
        });

        static::created(function ($companyProfile) {
            event(new CompanyProfileCreated($companyProfile));
        });

        static::deleted(function ($companyProfile) {
            event(new CompanyProfileDeleted($companyProfile));
        });
    }

    public static function getList()
    {
        return self::orderBy('company')->pluck('company', 'id')->all();
    }

    public static function inUse($id)
    {
        if (Invoice::where('company_profile_id', $id)->count()) {
            return true;
        }

        if (Quote::where('company_profile_id', $id)->count()) {
            return true;
        }

        if (Expense::where('company_profile_id', $id)->count()) {
            return true;
        }

        if (config('ip.defaultCompanyProfile') == $id) {
            return true;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function custom()
    {
        return $this->hasOne('IP\Modules\CustomFields\Models\CompanyProfileCustom');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedAddressAttribute()
    {
        return nl2br(formatAddress($this));
    }

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return route('companyProfiles.logo', [$this->id]);
        }
    }

    public function logo($width = null, $height = null)
    {
        if ($this->logo and file_exists(storage_path($this->logo))) {
            $logo = base64_encode(file_get_contents(storage_path($this->logo)));

            $style = '';

            if ($width and !$height) {
                $style = 'width: ' . $width . 'px;';
            } elseif ($width and $height) {
                $style = 'width: ' . $width . 'px; height: ' . $height . 'px;';
            }

            return '<img id="cp-logo" src="data:image/png;base64,' . $logo . '" style="' . $style . '">';
        }

        return null;
    }
}