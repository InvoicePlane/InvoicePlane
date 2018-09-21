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
use IP\Support\CurrencyFormatter;
use IP\Support\NumberFormatter;

class InvoiceAmount extends Model
{
    /**
     * Guarded properties
     *
     * @var array
     */
    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function invoice()
    {
        return $this->belongsTo('IP\Modules\Invoices\Models\Invoice');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedSubtotalAttribute()
    {
        return CurrencyFormatter::format($this->attributes['subtotal'], $this->invoice->currency);
    }

    public function getFormattedTaxAttribute()
    {
        return CurrencyFormatter::format($this->attributes['tax'], $this->invoice->currency);
    }

    public function getFormattedTotalAttribute()
    {
        return CurrencyFormatter::format($this->attributes['total'], $this->invoice->currency);
    }

    public function getFormattedPaidAttribute()
    {
        return CurrencyFormatter::format($this->attributes['paid'], $this->invoice->currency);
    }

    public function getFormattedBalanceAttribute()
    {
        return CurrencyFormatter::format($this->attributes['balance'], $this->invoice->currency);
    }

    public function getFormattedNumericBalanceAttribute()
    {
        return NumberFormatter::format($this->attributes['balance']);
    }

    public function getFormattedDiscountAttribute()
    {
        return CurrencyFormatter::format($this->attributes['discount'], $this->invoice->currency);
    }

    /**
     * Retrieve the formatted total prior to conversion.
     *
     * @return string
     */
    public function getFormattedTotalWithoutConversionAttribute()
    {
        return CurrencyFormatter::format($this->attributes['total'] / $this->invoice->exchange_rate);
    }
}
