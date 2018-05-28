<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\ClientCenter\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Invoices\Models\Invoice;
use FI\Support\Statuses\InvoiceStatuses;
use Illuminate\Support\Facades\DB;

class ClientCenterInvoiceController extends Controller
{
    private $invoiceStatuses;

    public function __construct(InvoiceStatuses $invoiceStatuses)
    {
        $this->invoiceStatuses = $invoiceStatuses;
    }

    public function index()
    {
        $invoices = Invoice::with(['amount.invoice.currency', 'client'])
            ->where('client_id', auth()->user()->client->id)
            ->orderBy('created_at', 'DESC')
            ->orderBy(DB::raw('length(number)'), 'DESC')
            ->orderBy('number', 'DESC')
            ->paginate(config('fi.resultsPerPage'));

        return view('client_center.invoices.index')
            ->with('invoices', $invoices)
            ->with('invoiceStatuses', $this->invoiceStatuses->statuses());
    }
}