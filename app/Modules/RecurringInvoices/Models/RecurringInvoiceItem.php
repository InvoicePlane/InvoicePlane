<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\RecurringInvoices\Models;

use FI\Events\RecurringInvoiceItemSaving;
use FI\Events\RecurringInvoiceModified;
use FI\Support\CurrencyFormatter;
use FI\Support\NumberFormatter;
use Illuminate\Database\Eloquent\Model;

class RecurringInvoiceItem extends Model
{
    /**
     * Guarded properties
     * @var array
     */
    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        static::saving(function($recurringInvoiceItem)
        {
            event(new RecurringInvoiceItemSaving($recurringInvoiceItem));
        });

        static::saved(function($recurringInvoiceItem)
        {
            event(new RecurringInvoiceModified($recurringInvoiceItem->recurringInvoice));
        });

        static::deleting(function ($recurringInvoiceItem)
        {
            $recurringInvoiceItem->amount()->delete();
        });

        static::deleted(function($recurringInvoiceItem)
        {
            if ($recurringInvoiceItem->recurringInvoice)
            {
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
        return $this->hasOne('FI\Modules\RecurringInvoices\Models\RecurringInvoiceItemAmount', 'item_id');
    }

    public function recurringInvoice()
    {
        return $this->belongsTo('FI\Modules\RecurringInvoices\Models\RecurringInvoice');
    }

    public function taxRate()
    {
        return $this->belongsTo('FI\Modules\TaxRates\Models\TaxRate');
    }

    public function taxRate2()
    {
        return $this->belongsTo('FI\Modules\TaxRates\Models\TaxRate', 'tax_rate_2_id');
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