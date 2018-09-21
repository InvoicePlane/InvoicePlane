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

namespace IP\Modules\RecurringInvoices\Models;

use Illuminate\Database\Eloquent\Model;
use IP\Events\RecurringInvoiceItemSaving;
use IP\Events\RecurringInvoiceModified;
use IP\Support\CurrencyFormatter;
use IP\Support\NumberFormatter;

class RecurringInvoiceItem extends Model
{
    /**
     * Guarded properties
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($recurringInvoiceItem) {
            event(new RecurringInvoiceItemSaving($recurringInvoiceItem));
        });

        static::saved(function ($recurringInvoiceItem) {
            event(new RecurringInvoiceModified($recurringInvoiceItem->recurringInvoice));
        });

        static::deleting(function ($recurringInvoiceItem) {
            $recurringInvoiceItem->amount()->delete();
        });

        static::deleted(function ($recurringInvoiceItem) {
            if ($recurringInvoiceItem->recurringInvoice) {
                event(new RecurringInvoiceModified($recurringInvoiceItem->recurringInvoice));
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function amount()
    {
        return $this->hasOne('IP\Modules\RecurringInvoices\Models\RecurringInvoiceItemAmount', 'item_id');
    }

    public function recurringInvoice()
    {
        return $this->belongsTo('IP\Modules\RecurringInvoices\Models\RecurringInvoice');
    }

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

    public function getFormattedQuantityAttribute()
    {
        return NumberFormatter::format($this->attributes['quantity']);
    }

    public function getFormattedNumericPriceAttribute()
    {
        return NumberFormatter::format($this->attributes['price']);
    }

    public function getFormattedPriceAttribute()
    {
        return CurrencyFormatter::format($this->attributes['price'], $this->recurringInvoice->currency);
    }

    public function getFormattedDescriptionAttribute()
    {
        return nl2br($this->attributes['description']);
    }
}
