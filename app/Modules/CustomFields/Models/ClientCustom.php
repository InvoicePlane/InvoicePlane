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

namespace FI\Modules\CustomFields\Models;

use FI\Events\ClientCustomCreating;
use Illuminate\Database\Eloquent\Model;

class ClientCustom extends Model
{
    protected $table = 'clients_custom';

    protected $primaryKey = 'client_id';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($clientCustom) {
            event(new ClientCustomCreating($clientCustom));
        });
    }
}