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

namespace IP\Modules\Currencies\Models;

use IP\Modules\Clients\Models\Client;
use IP\Modules\Invoices\Models\Invoice;
use IP\Modules\Quotes\Models\Quote;
use IP\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use Sortable;

    protected $table = 'currencies';

    protected $sortable = ['code', 'name', 'symbol', 'placement', 'decimal', 'thousands'];

    /**
     * Guarded properties
     * @var array
     */
    protected $guarded = ['id'];

    public static function getList()
    {
        return self::orderBy('name')->pluck('name', 'code')->all();
    }

    public function getInUseAttribute()
    {
        if ($this->code == config('fi.baseCurrency')) {
            return true;
        }

        if (Client::where('currency_code', '=', $this->code)->count()) {
            return true;
        }

        if (Quote::where('currency_code', '=', $this->code)->count()) {
            return true;
        }

        if (Invoice::where('currency_code', '=', $this->code)->count()) {
            return true;
        }

        return false;
    }

    public function getFormattedPlacementAttribute()
    {
        return ($this->placement == 'before') ? trans('ip.before_amount') : trans('ip.after_amount');
    }
}