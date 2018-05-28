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

class ExpenseCustom extends Model
{
    protected $table = 'expenses_custom';

    protected $primaryKey = 'expense_id';

    protected $guarded = [];
}