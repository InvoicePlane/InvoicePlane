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

namespace IP\Modules\PaymentMethods\Models;

use IP\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use Sortable;

    protected $guarded = ['id'];

    protected $sortable = ['name'];

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    public static function getList()
    {
        return self::orderBy('name')->pluck('name', 'id')->all();
    }
}