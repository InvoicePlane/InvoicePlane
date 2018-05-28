<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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

        static::creating(function ($clientCustom)
        {
            event(new ClientCustomCreating($clientCustom));
        });
    }
}