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

namespace FI\Modules\ClientCenter\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Payments\Models\Payment;

class ClientCenterPaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('invoice.amount.invoice.currency', 'invoice.client')
            ->whereHas('invoice', function ($invoice) {
                $invoice->where('client_id', auth()->user()->client->id);
            })->orderBy('created_at', 'desc')
            ->paginate(config('fi.resultsPerPage'));

        return view('client_center.payments.index')
            ->with('payments', $payments);
    }
}