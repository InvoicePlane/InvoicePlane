<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\RecurringInvoices\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\RecurringInvoices\Models\RecurringInvoiceItem;

class RecurringInvoiceItemController extends Controller
{
    public function delete()
    {
        RecurringInvoiceItem::destroy(request('id'));
    }
}