<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Exports\Support\Results;

use FI\Modules\Quotes\Models\QuoteItem;

class QuoteItems implements SourceInterface
{
    public function getResults($params = [])
    {
        $quoteItem = QuoteItem::select('quotes.number', 'quote_items.created_at', 'quote_items.name',
            'quote_items.description', 'quote_items.quantity', 'quote_items.price', 'tax_rate_1.name AS tax_rate_1_name',
            'tax_rate_1.percent AS tax_rate_1_percent', 'tax_rate_1.is_compound AS tax_rate_1_is_compound',
            'quote_item_amounts.tax_1 AS tax_rate_1_amount', 'tax_rate_2.name AS tax_rate_2_name',
            'tax_rate_2.percent AS tax_rate_2_percent', 'tax_rate_2.is_compound AS tax_rate_2_is_compound',
            'quote_item_amounts.tax_2 AS tax_rate_2_amount', 'quote_item_amounts.subtotal', 'quote_item_amounts.tax',
            'quote_item_amounts.total')
            ->join('quotes', 'quotes.id', '=', 'quote_items.quote_id')
            ->join('quote_item_amounts', 'quote_item_amounts.item_id', '=', 'quote_items.id')
            ->leftJoin('tax_rates AS tax_rate_1', 'tax_rate_1.id', '=', 'quote_items.tax_rate_id')
            ->leftJoin('tax_rates AS tax_rate_2', 'tax_rate_2.id', '=', 'quote_items.tax_rate_2_id')
            ->orderBy('quotes.number');

        return $quoteItem->get()->toArray();
    }
}