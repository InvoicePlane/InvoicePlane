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

namespace FI\Modules\Exports\Support\Results;

use FI\Modules\Payments\Models\Payment;

class Payments implements SourceInterface
{
    public function getResults($params = [])
    {
        $payment = Payment::select('invoices.number', 'payments.paid_at', 'payments.amount',
            'payment_methods.name AS payment_method', 'payments.note')
            ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'payment_method_id')
            ->orderBy('invoices.number');

        return $payment->get()->toArray();
    }
}