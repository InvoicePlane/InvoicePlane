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

namespace IP\Modules\Quotes\Support;

use IP\Modules\Quotes\Models\Quote;
use IP\Modules\Quotes\Models\QuoteAmount;
use IP\Modules\Quotes\Models\QuoteItem;
use IP\Modules\Quotes\Models\QuoteItemAmount;

class QuoteCalculate
{
    public function calculateAll()
    {
        foreach (Quote::get() as $quote) {
            $this->calculate($quote);
        }
    }

    public function calculate($quote)
    {
        $quoteItems = QuoteItem::select('quote_items.*',
            'tax_rates_1.percent AS tax_rate_1_percent',
            'tax_rates_2.percent AS tax_rate_2_percent',
            'tax_rates_2.is_compound AS tax_rate_2_is_compound',
            'tax_rates_1.calculate_vat AS tax_rate_1_calculate_vat')
            ->leftJoin('tax_rates AS tax_rates_1', 'quote_items.tax_rate_id', '=', 'tax_rates_1.id')
            ->leftJoin('tax_rates AS tax_rates_2', 'quote_items.tax_rate_2_id', '=', 'tax_rates_2.id')
            ->where('quote_id', $quote->id)
            ->get();

        $calculator = new QuoteCalculator;
        $calculator->setId($quote->id);
        $calculator->setDiscount($quote->discount);

        foreach ($quoteItems as $quoteItem) {
            $taxRatePercent = ($quoteItem->tax_rate_id) ? $quoteItem->tax_rate_1_percent : 0;
            $taxRate2Percent = ($quoteItem->tax_rate_2_id) ? $quoteItem->tax_rate_2_percent : 0;
            $taxRate2IsCompound = ($quoteItem->tax_rate_2_is_compound) ? 1 : 0;
            $taxRate1CalculateVat = ($quoteItem->tax_rate_1_calculate_vat) ? 1 : 0;

            $calculator->addItem(
                $quoteItem->id,
                $quoteItem->quantity,
                $quoteItem->price,
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
            $quoteItemAmount = QuoteItemAmount::firstOrNew(['item_id' => $calculatedItemAmount['item_id']]);
            $quoteItemAmount->fill($calculatedItemAmount);
            $quoteItemAmount->save();
        }

        // Update the overall quote amount record
        $quoteAmount = QuoteAmount::firstOrNew(['quote_id' => $quote->id]);
        $quoteAmount->fill($calculatedAmount);
        $quoteAmount->save();
    }
}
