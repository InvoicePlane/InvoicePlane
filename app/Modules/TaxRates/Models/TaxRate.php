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

namespace IP\Modules\TaxRates\Models;

use Illuminate\Database\Eloquent\Model;
use IP\Modules\Invoices\Models\InvoiceItem;
use IP\Modules\Quotes\Models\QuoteItem;
use IP\Modules\RecurringInvoices\Models\RecurringInvoiceItem;
use IP\Support\NumberFormatter;
use IP\Traits\Sortable;

class TaxRate extends Model
{
    use Sortable;

    /**
     * Guarded properties
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected $sortable = ['name', 'percent', 'is_compound'];

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    public static function getList()
    {
        return ['0' => trans('ip.none')] + self::pluck('name', 'id')->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedPercentAttribute()
    {
        return NumberFormatter::format($this->attributes['percent'], null, 3) . '%';
    }

    public function getFormattedNumericPercentAttribute()
    {
        return NumberFormatter::format($this->attributes['percent'], null, 3);
    }

    public function getFormattedIsCompoundAttribute()
    {
        return ($this->attributes['is_compound']) ? trans('ip.yes') : trans('ip.no');
    }

    public function getInUseAttribute()
    {
        if (InvoiceItem::where('tax_rate_id', $this->id)->orWhere('tax_rate_2_id', $this->id)->count()) {
            return true;
        }

        if (RecurringInvoiceItem::where('tax_rate_id', $this->id)->orWhere('tax_rate_2_id', $this->id)->count()) {
            return true;
        }

        if (QuoteItem::where('tax_rate_id', $this->id)->orWhere('tax_rate_2_id', $this->id)->count()) {
            return true;
        }

        if (config('ip.itemTaxRate') == $this->id or config('ip.itemTax2Rate') == $this->id) {
            return true;
        }

        return false;
    }
}
