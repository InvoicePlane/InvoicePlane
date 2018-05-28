<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\TaxRates\Models;

use FI\Modules\Invoices\Models\InvoiceItem;
use FI\Modules\Quotes\Models\QuoteItem;
use FI\Modules\RecurringInvoices\Models\RecurringInvoiceItem;
use FI\Support\NumberFormatter;
use FI\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    use Sortable;

    /**
     * Guarded properties
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
        return ['0' => trans('fi.none')] + self::pluck('name', 'id')->all();
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
        return ($this->attributes['is_compound']) ? trans('fi.yes') : trans('fi.no');
    }

    public function getInUseAttribute()
    {
        if (InvoiceItem::where('tax_rate_id', $this->id)->orWhere('tax_rate_2_id', $this->id)->count())
        {
            return true;
        }

        if (RecurringInvoiceItem::where('tax_rate_id', $this->id)->orWhere('tax_rate_2_id', $this->id)->count())
        {
            return true;
        }

        if (QuoteItem::where('tax_rate_id', $this->id)->orWhere('tax_rate_2_id', $this->id)->count())
        {
            return true;
        }

        if (config('fi.itemTaxRate') == $this->id or config('fi.itemTax2Rate') == $this->id)
        {
            return true;
        }

        return false;
    }
}