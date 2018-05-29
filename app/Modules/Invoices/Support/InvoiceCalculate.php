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

namespace FI\Modules\Invoices\Support;

use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Invoices\Models\InvoiceAmount;
use FI\Modules\Invoices\Models\InvoiceItem;
use FI\Modules\Invoices\Models\InvoiceItemAmount;
use FI\Modules\Payments\Models\Payment;
use FI\Support\Statuses\InvoiceStatuses;

class InvoiceCalculate
{
    public function calculateAll()
    {
        foreach (Invoice::get() as $invoice) {
            $this->calculate($invoice);
        }
    }

    public function calculate($invoice)
    {
        $invoiceItems = InvoiceItem::select('invoice_items.*',
            'tax_rates_1.percent AS tax_rate_1_percent',
            'tax_rates_2.percent AS tax_rate_2_percent',
            'tax_rates_2.is_compound AS tax_rate_2_is_compound',
            'tax_rates_1.calculate_vat AS tax_rate_1_calculate_vat')
            ->leftJoin('tax_rates AS tax_rates_1', 'invoice_items.tax_rate_id', '=', 'tax_rates_1.id')
            ->leftJoin('tax_rates AS tax_rates_2', 'invoice_items.tax_rate_2_id', '=', 'tax_rates_2.id')
            ->where('invoice_id', $invoice->id)
            ->get();

        $totalPaid = Payment::where('invoice_id', $invoice->id)->sum('amount');

        $calculator = new InvoiceCalculator;
        $calculator->setId($invoice->id);
        $calculator->setTotalPaid($totalPaid);
        $calculator->setDiscount($invoice->discount);

        if ($invoice->status_text == 'canceled') {
            $calculator->setIsCanceled(true);
        }

        foreach ($invoiceItems as $invoiceItem) {
            $taxRatePercent = ($invoiceItem->tax_rate_id) ? $invoiceItem->tax_rate_1_percent : 0;
            $taxRate2Percent = ($invoiceItem->tax_rate_2_id) ? $invoiceItem->tax_rate_2_percent : 0;
            $taxRate2IsCompound = ($invoiceItem->tax_rate_2_is_compound) ? 1 : 0;
            $taxRate1CalculateVat = ($invoiceItem->tax_rate_1_calculate_vat) ? 1 : 0;

            $calculator->addItem($invoiceItem->id, $invoiceItem->quantity, $invoiceItem->price, $taxRatePercent, $taxRate2Percent, $taxRate2IsCompound, $taxRate1CalculateVat);
        }

        $calculator->calculate();

        // Get the calculated values
        $calculatedItemAmounts = $calculator->getCalculatedItemAmounts();
        $calculatedAmount = $calculator->getCalculatedAmount();

        // Update the item amount records.
        foreach ($calculatedItemAmounts as $calculatedItemAmount) {
            $invoiceItemAmount = InvoiceItemAmount::firstOrNew(['item_id' => $calculatedItemAmount['item_id']]);
            $invoiceItemAmount->fill($calculatedItemAmount);
            $invoiceItemAmount->save();
        }

        // Update the overall invoice amount record.
        $invoiceAmount = InvoiceAmount::firstOrNew(['invoice_id' => $invoice->id]);
        $invoiceAmount->fill($calculatedAmount);
        $invoiceAmount->save();

        // Check to see if the invoice should be marked as paid.
        if ($calculatedAmount['total'] > 0 and $calculatedAmount['balance'] <= 0 and $invoice->status_text != 'canceled') {
            $invoice->invoice_status_id = InvoiceStatuses::getStatusId('paid');
            $invoice->save();
        }

        // Check to see if the invoice was marked as paid but should no longer be.
        if ($calculatedAmount['total'] > 0 and $calculatedAmount['balance'] > 0 and $invoice->invoice_status_id == InvoiceStatuses::getStatusId('paid')) {
            $invoice->invoice_status_id = InvoiceStatuses::getStatusId('sent');
            $invoice->save();
        }
    }
}