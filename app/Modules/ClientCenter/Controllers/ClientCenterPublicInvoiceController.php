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

use FI\Events\InvoiceViewed;
use FI\Http\Controllers\Controller;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Merchant\Support\MerchantFactory;
use FI\Support\FileNames;
use FI\Support\PDF\PDFFactory;
use FI\Support\Statuses\InvoiceStatuses;

class ClientCenterPublicInvoiceController extends Controller
{
    public function show($urlKey)
    {
        $invoice = Invoice::where('url_key', $urlKey)->first();

        app()->setLocale($invoice->client->language);

        event(new InvoiceViewed($invoice));

        return view('client_center.invoices.public')
            ->with('invoice', $invoice)
            ->with('statuses', InvoiceStatuses::statuses())
            ->with('urlKey', $urlKey)
            ->with('merchantDrivers', MerchantFactory::getDrivers(true))
            ->with('attachments', $invoice->clientAttachments);
    }

    public function pdf($urlKey)
    {
        $invoice = Invoice::with('items.taxRate', 'items.taxRate2', 'items.amount.item.invoice', 'items.invoice')
            ->where('url_key', $urlKey)->first();

        event(new InvoiceViewed($invoice));

        $pdf = PDFFactory::create();

        $pdf->download($invoice->html, FileNames::invoice($invoice));
    }

    public function html($urlKey)
    {
        $invoice = Invoice::with('items.taxRate', 'items.taxRate2', 'items.amount.item.invoice', 'items.invoice')
            ->where('url_key', $urlKey)->first();

        return $invoice->html;
    }
}