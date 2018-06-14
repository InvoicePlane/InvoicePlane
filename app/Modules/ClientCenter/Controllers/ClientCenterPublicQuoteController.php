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

namespace IP\Modules\ClientCenter\Controllers;

use IP\Events\QuoteApproved;
use IP\Events\QuoteRejected;
use IP\Events\QuoteViewed;
use IP\Http\Controllers\Controller;
use IP\Modules\Quotes\Models\Quote;
use IP\Support\FileNames;
use IP\Support\PDF\PDFFactory;
use IP\Support\Statuses\QuoteStatuses;

class ClientCenterPublicQuoteController extends Controller
{
    public function show($urlKey)
    {
        $quote = Quote::where('url_key', $urlKey)->first();

        app()->setLocale($quote->client->language);

        event(new QuoteViewed($quote));

        return view('client_center.quotes.public')
            ->with('quote', $quote)
            ->with('statuses', QuoteStatuses::statuses())
            ->with('urlKey', $urlKey)
            ->with('attachments', $quote->clientAttachments);
    }

    public function pdf($urlKey)
    {
        $quote = Quote::with('items.taxRate', 'items.taxRate2', 'items.amount.item.quote', 'items.quote')
            ->where('url_key', $urlKey)->first();

        event(new QuoteViewed($quote));

        $pdf = PDFFactory::create();

        $pdf->download($quote->html, FileNames::quote($quote));
    }

    public function html($urlKey)
    {
        $quote = Quote::with('items.taxRate', 'items.taxRate2', 'items.amount.item.quote', 'items.quote')
            ->where('url_key', $urlKey)->first();

        return $quote->html;
    }

    public function approve($urlKey)
    {
        $quote = Quote::where('url_key', $urlKey)->first();

        $quote->quote_status_id = QuoteStatuses::getStatusId('approved');

        $quote->save();

        event(new QuoteApproved($quote));

        return redirect()->route('clientCenter.public.quote.show', [$urlKey]);
    }

    public function reject($urlKey)
    {
        $quote = Quote::where('url_key', $urlKey)->first();

        $quote->quote_status_id = QuoteStatuses::getStatusId('rejected');

        $quote->save();

        event(new QuoteRejected($quote));

        return redirect()->route('clientCenter.public.quote.show', [$urlKey]);
    }
}