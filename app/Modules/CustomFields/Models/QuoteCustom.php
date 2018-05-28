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

use Illuminate\Database\Eloquent\Model;

class QuoteCustom extends Model
{
    /**
     * The table name
     * @var string
     */
    protected $table = 'quotes_custom';

    /**
     * The primary key
     * @var string
     */
    protected $primaryKey = 'quote_id';

    /**
     * Guarded properties
     * @var array
     */
    protected $guarded = [];
}