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

namespace FI\Modules\Reports\Reports;

use FI\Modules\Payments\Models\Payment;
use FI\Support\CurrencyFormatter;
use FI\Support\DateFormatter;

class PaymentsCollectedReport
{
    public function getResults($fromDate, $toDate, $companyProfileId = null)
    {
        $results = [
            'from_date' => DateFormatter::format($fromDate),
            'to_date' => DateFormatter::format($toDate),
            'payments' => [],
            'total' => 0,
        ];

        $payments = Payment::select('payments.*')
            ->with(['invoice.client', 'paymentMethod'])
            ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->dateRange($fromDate, $toDate);

        if ($companyProfileId) {
            $payments->where('invoices.company_profile_id', $companyProfileId);
        }

        $payments = $payments->get();

        foreach ($payments as $payment) {
            $results['payments'][] = [
                'client_name' => $payment->invoice->client->name,
                'invoice_number' => $payment->invoice->number,
                'payment_method' => isset($payment->paymentMethod->name) ? $payment->paymentMethod->name : '',
                'note' => $payment->note,
                'date' => $payment->formatted_paid_at,
                'amount' => CurrencyFormatter::format($payment->amount / $payment->invoice->exchange_rate),
            ];

            $results['total'] += $payment->amount / $payment->invoice->exchange_rate;
        }

        $results['total'] = CurrencyFormatter::format($results['total']);

        return $results;
    }
}