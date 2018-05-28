<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Addons\Models;

use FI\Support\Migrations;
use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    protected $table = 'addons';

    protected $guarded = ['id'];

    public function getHasPendingMigrationsAttribute()
    {
        $migrations = new Migrations();

        if ($migrations->getPendingMigrations(addon_path($this->path . '/Migrations')))
        {
            return true;
        }

        return false;
    }
}