<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\ClientCenter\Controllers;

use FI\Events\QuoteApproved;
use FI\Events\QuoteRejected;
use FI\Events\QuoteViewed;
use FI\Http\Controllers\Controller;
use FI\Modules\Quotes\Models\Quote;
use FI\Support\FileNames;
use FI\Support\PDF\PDFFactory;
use FI\Support\Statuses\QuoteStatuses;

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