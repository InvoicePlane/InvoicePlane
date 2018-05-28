<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
        return ($this->default_bcc) ? trans('fi.yes') : trans('fi.no');
    }

    public function getFormattedDefaultCcAttribute()
    {
        return ($this->default_cc) ? trans('fi.yes') : trans('fi.no');
    }

    public function getFormattedDefaultToAttribute()
    {
        return ($this->default_to) ? trans('fi.yes') : trans('fi.no');
    }
}