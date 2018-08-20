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

namespace IP\Modules\Invoices\Models;

use Illuminate\Database\Eloquent\Model;
use IP\Events\InvoiceItemSaving;
use IP\Events\InvoiceModified;
use IP\Support\CurrencyFormatter;
use IP\Support\NumberFormatter;

class InvoiceItem extends Model
{
    protected $guarded = ['id', 'item_id'];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($invoiceItem) {
            event(new InvoiceItemSaving($invoiceItem));
        });

        static::saved(function ($invoiceItem) {
            event(new InvoiceModified($invoiceItem->invoice));
        });

        static::deleting(function ($invoiceItem) {
            $invoiceItem->amount()->delete();
        });

        static::deleted(function ($invoiceItem) {
            if ($invoiceItem->invoice) {
                event(new InvoiceModified($invoiceItem->invoice));
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
        return $this->hasOne('IP\Modules\Invoices\Models\InvoiceItemAmount', 'item_id');
    }

    public function invoice()
    {
        return $this->belongsTo('IP\Modules\Invoices\Models\Invoice');
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
        return CurrencyFormatter::format($this->attributes['price'], $this->invoice->currency);
    }

    public function getFormattedDescriptionAttribute()
    {
        return nl2br($this->attributes['description']);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereIn('invoice_id', function ($query) use ($from, $to) {
            $query->select('id')
                ->from('invoices')
                ->where('invoice_date', '>=', $from)
                ->where('invoice_date', '<=', $to);
        });
    }
}
