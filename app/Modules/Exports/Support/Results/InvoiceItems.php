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

use FI\Modules\Invoices\Models\InvoiceItem;

class InvoiceItems implements SourceInterface
{
    public function getResults($params = [])
    {
        $invoiceItem = InvoiceItem::select('invoices.number', 'invoice_items.created_at', 'invoice_items.name',
            'invoice_items.description', 'invoice_items.quantity', 'invoice_items.price', 'tax_rate_1.name AS tax_rate_1_name',
            'tax_rate_1.percent AS tax_rate_1_percent', 'tax_rate_1.is_compound AS tax_rate_1_is_compound',
            'invoice_item_amounts.tax_1 AS tax_rate_1_amount', 'tax_rate_2.name AS tax_rate_2_name',
            'tax_rate_2.percent AS tax_rate_2_percent', 'tax_rate_2.is_compound AS tax_rate_2_is_compound',
            'invoice_item_amounts.tax_2 AS tax_rate_2_amount', 'invoice_item_amounts.subtotal', 'invoice_item_amounts.tax',
            'invoice_item_amounts.total')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->join('invoice_item_amounts', 'invoice_item_amounts.item_id', '=', 'invoice_items.id')
            ->leftJoin('tax_rates AS tax_rate_1', 'tax_rate_1.id', '=', 'invoice_items.tax_rate_id')
            ->leftJoin('tax_rates AS tax_rate_2', 'tax_rate_2.id', '=', 'invoice_items.tax_rate_2_id')
            ->orderBy('invoices.number');

        return $invoiceItem->get()->toArray();
    }
}