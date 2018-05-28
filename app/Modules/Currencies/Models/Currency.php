<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Currencies\Models;

use FI\Modules\Clients\Models\Client;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Quotes\Models\Quote;
use FI\Traits\Sortable;
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
        if ($this->code == config('fi.baseCurrency'))
        {
            return true;
        }

        if (Client::where('currency_code', '=', $this->code)->count())
        {
            return true;
        }

        if (Quote::where('currency_code', '=', $this->code)->count())
        {
            return true;
        }

        if (Invoice::where('currency_code', '=', $this->code)->count())
        {
            return true;
        }

        return false;
    }

    public function getFormattedPlacementAttribute()
    {
        return ($this->placement == 'before') ? trans('fi.before_amount') : trans('fi.after_amount');
    }
}