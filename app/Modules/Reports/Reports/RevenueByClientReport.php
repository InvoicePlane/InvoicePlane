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

namespace IP\Modules\Reports\Reports;

use IP\Modules\Payments\Models\Payment;
use IP\Support\CurrencyFormatter;

class RevenueByClientReport
{
    public function getResults($companyProfileId = null, $year)
    {
        $results = [];

        $payments = Payment::select('payments.*')
            ->with(['invoice.client'])
            ->year($year)
            ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->join('clients', 'clients.id', '=', 'invoices.client_id')
            ->orderBy('clients.name');

        if ($companyProfileId) {
            $payments->where('company_profile_id', $companyProfileId);
        }

        $payments = $payments->get();

        foreach ($payments as $payment) {
            if (isset($results[$payment->invoice->client->name]['months'][date('n', strtotime($payment->paid_at))])) {
                $results[$payment->invoice->client->name]['months'][date('n',
                    strtotime($payment->paid_at))] += $payment->amount / $payment->invoice->exchange_rate;
            } else {
                $results[$payment->invoice->client->name]['months'][date('n',
                    strtotime($payment->paid_at))] = $payment->amount / $payment->invoice->exchange_rate;
            }
        }

        foreach ($results as $client => $result) {
            $results[$client]['total'] = 0;

            foreach (range(1, 12) as $month) {
                if (!isset($results[$client]['months'][$month])) {
                    $results[$client]['months'][$month] = CurrencyFormatter::format(0);
                } else {
                    $results[$client]['total'] += $results[$client]['months'][$month];
                    $results[$client]['months'][$month] = CurrencyFormatter::format($results[$client]['months'][$month]);
                }
            }
            $results[$client]['total'] = CurrencyFormatter::format($results[$client]['total']);
        }

        return $results;
    }
}
