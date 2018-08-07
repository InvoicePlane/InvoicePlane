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

use IP\Events\InvoiceModified;
use IP\Modules\CustomFields\Models\CustomField;
use IP\Modules\Groups\Models\Group;
use IP\Modules\Invoices\Models\Invoice;
use IP\Modules\Invoices\Models\InvoiceItem;
use IP\Support\Statuses\InvoiceStatuses;
use IP\Support\Statuses\QuoteStatuses;

class QuoteToInvoice
{
    public function convert($quote, $invoiceDate, $dueAt, $groupId)
    {
        $record = [
            'client_id' => $quote->client_id,
            'invoice_date' => $invoiceDate,
            'due_at' => $dueAt,
            'group_id' => $groupId,
            'number' => Group::generateNumber($groupId),
            'user_id' => $quote->user_id,
            'invoice_status_id' => InvoiceStatuses::getStatusId('draft'),
            'terms' => ((config('ip.convertQuoteTerms') == 'quote') ? $quote->terms : config('ip.invoiceTerms')),
            'footer' => $quote->footer,
            'currency_code' => $quote->currency_code,
            'exchange_rate' => $quote->exchange_rate,
            'summary' => $quote->summary,
            'discount' => $quote->discount,
            'company_profile_id' => $quote->company_profile_id,
        ];

        $toInvoice = Invoice::create($record);

        CustomField::copyCustomFieldValues($quote, $toInvoice);

        $quote->invoice_id = $toInvoice->id;
        $quote->quote_status_id = QuoteStatuses::getStatusId('approved');
        $quote->save();

        foreach ($quote->quoteItems as $item) {
            $itemRecord = [
                'invoice_id' => $toInvoice->id,
                'name' => $item->name,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'tax_rate_id' => $item->tax_rate_id,
                'tax_rate_2_id' => $item->tax_rate_2_id,
                'display_order' => $item->display_order,
            ];

            InvoiceItem::create($itemRecord);
        }

        event(new InvoiceModified($toInvoice));

        return $toInvoice;
    }
}