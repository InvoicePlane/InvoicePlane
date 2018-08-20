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

namespace IP\Modules\Addons\Models;

use Illuminate\Database\Eloquent\Model;
use IP\Support\Migrations;

class Addon extends Model
{
    protected $table = 'addons';

    protected $guarded = ['id'];

    public function getHasPendingMigrationsAttribute()
    {
        $migrations = new Migrations();

        if ($migrations->getPendingMigrations(addon_path($this->path . '/Migrations'))) {
            return true;
        }

        return false;
    }
}
