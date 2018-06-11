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

namespace FI\Modules\Clients\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function client()
    {
        return $this->belongsTo('FI\Modules\Clients\Models\Client');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedContactAttribute()
    {
        return $this->name . ' <' . $this->email . '>';
    }

    public function getFormattedDefaultBccAttribute()
    {
        return ($this->default_bcc) ? trans('ip.yes') : trans('ip.no');
    }

    public function getFormattedDefaultCcAttribute()
    {
        return ($this->default_cc) ? trans('ip.yes') : trans('ip.no');
    }

    public function getFormattedDefaultToAttribute()
    {
        return ($this->default_to) ? trans('ip.yes') : trans('ip.no');
    }
}