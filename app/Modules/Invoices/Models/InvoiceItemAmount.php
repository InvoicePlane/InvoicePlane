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

class InvoiceItemAmount extends Model
{
    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function item()
    {
        return $this->belongsTo('IP\Modules\Invoices\Models\InvoiceItem');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedSubtotalAttribute()
    {
        return CurrencyFormatter::format($this->attributes['subtotal'], $this->item->invoice->currency);
    }

    public function getFormattedTaxAttribute()
    {
        return CurrencyFormatter::format($this->attributes['tax'], $this->item->invoice->currency);
    }

    public function getFormattedTax1Attribute()
    {
        return CurrencyFormatter::format($this->attributes['tax_1'], $this->item->invoice->currency);
    }

    public function getFormattedTax2Attribute()
    {
        return CurrencyFormatter::format($this->attributes['tax_2'], $this->item->invoice->currency);
    }

    public function getFormattedTotalAttribute()
    {
        return CurrencyFormatter::format($this->attributes['total'], $this->item->invoice->currency);
    }
}
