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

namespace IP\Modules\ItemLookups\Models;

use IP\Support\CurrencyFormatter;
use IP\Support\NumberFormatter;
use IP\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ItemLookup extends Model
{
    use Sortable;

    /**
     * Guarded properties
     * @var array
     */
    protected $guarded = ['id'];

    protected $sortable = ['name', 'description', 'price'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function taxRate()
    {
        return $this->belongsTo('IP\Modules\TaxRates\Models\TaxRate');
    }

    public function taxRate2()
    {
        return $this->belongsTo('IP\Modules\TaxRates\Models\TaxRate', 'tax_rate_2_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedPriceAttribute()
    {
        return CurrencyFormatter::format($this->attributes['price']);
    }

    public function getFormattedNumericPriceAttribute()
    {
        return NumberFormatter::format($this->attributes['price']);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeKeywords($query, $keywords)
    {
        if ($keywords) {
            $keywords = explode(' ', $keywords);

            foreach ($keywords as $keyword) {
                if ($keyword) {
                    $keyword = strtolower($keyword);

                    $query->where(DB::raw("CONCAT_WS('^',LOWER(name),LOWER(description),price)"), 'LIKE', "%$keyword%");
                }
            }
        }

        return $query;
    }
}