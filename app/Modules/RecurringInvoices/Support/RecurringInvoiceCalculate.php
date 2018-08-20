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

namespace IP\Modules\RecurringInvoices\Support;

use IP\Modules\RecurringInvoices\Models\RecurringInvoice;
use IP\Modules\RecurringInvoices\Models\RecurringInvoiceAmount;
use IP\Modules\RecurringInvoices\Models\RecurringInvoiceItem;
use IP\Modules\RecurringInvoices\Models\RecurringInvoiceItemAmount;

class RecurringInvoiceCalculate
{
    public function calculateAll()
    {
        $recurringInvoiceIds = RecurringInvoice::select('id')->get();

        foreach ($recurringInvoiceIds as $recurringInvoiceId) {
            $this->calculate($recurringInvoiceId->id);
        }
    }

    public function calculate($recurringInvoiceId)
    {
        $recurringInvoice = RecurringInvoice::find($recurringInvoiceId);

        $recurringInvoiceItems = RecurringInvoiceItem::select('recurring_invoice_items.*',
            'tax_rates_1.percent AS tax_rate_1_percent',
            'tax_rates_2.percent AS tax_rate_2_percent',
            'tax_rates_2.is_compound AS tax_rate_2_is_compound',
            'tax_rates_1.calculate_vat AS tax_rate_1_calculate_vat')
            ->leftJoin('tax_rates AS tax_rates_1', 'recurring_invoice_items.tax_rate_id', '=', 'tax_rates_1.id')
            ->leftJoin('tax_rates AS tax_rates_2', 'recurring_invoice_items.tax_rate_2_id', '=', 'tax_rates_2.id')
            ->where('recurring_invoice_id', $recurringInvoiceId)
            ->get();

        $calculator = new RecurringInvoiceCalculator;
        $calculator->setId($recurringInvoiceId);
        $calculator->setDiscount($recurringInvoice->discount);

        foreach ($recurringInvoiceItems as $recurringInvoiceItem) {
            $taxRatePercent = ($recurringInvoiceItem->tax_rate_id) ? $recurringInvoiceItem->tax_rate_1_percent : 0;
            $taxRate2Percent = ($recurringInvoiceItem->tax_rate_2_id) ? $recurringInvoiceItem->tax_rate_2_percent : 0;
            $taxRate2IsCompound = ($recurringInvoiceItem->tax_rate_2_is_compound) ? 1 : 0;
            $taxRate1CalculateVat = ($recurringInvoiceItem->tax_rate_1_calculate_vat) ? 1 : 0;

            $calculator->addItem(
                $recurringInvoiceItem->id,
                $recurringInvoiceItem->quantity,
                $recurringInvoiceItem->price,
                $taxRatePercent,
                $taxRate2Percent,
                $taxRate2IsCompound,
                $taxRate1CalculateVat
            );
        }

        $calculator->calculate();

        // Get the calculated values
        $calculatedItemAmounts = $calculator->getCalculatedItemAmounts();
        $calculatedAmount = $calculator->getCalculatedAmount();

        // Update the item amount records
        foreach ($calculatedItemAmounts as $calculatedItemAmount) {
            $recurringInvoiceItemAmount = RecurringInvoiceItemAmount::firstOrNew(['item_id' => $calculatedItemAmount['item_id']]);
            $recurringInvoiceItemAmount->fill($calculatedItemAmount);
            $recurringInvoiceItemAmount->save();
        }

        // Update the overall recurringInvoice amount record
        $recurringInvoiceAmount = RecurringInvoiceAmount::firstOrNew(['recurring_invoice_id' => $recurringInvoiceId]);
        $recurringInvoiceAmount->fill($calculatedAmount);
        $recurringInvoiceAmount->save();
    }
}
