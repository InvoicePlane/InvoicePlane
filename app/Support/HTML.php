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

namespace FI\Support;

use FI\Events\InvoiceHTMLCreating;
use FI\Events\QuoteHTMLCreating;

class HTML
{
    public static function invoice($invoice)
    {
        app()->setLocale($invoice->client->language);

        config(['fi.baseCurrency' => $invoice->currency_code]);

        event(new InvoiceHTMLCreating($invoice));

        $template = str_replace('.blade.php', '', $invoice->template);

        if (view()->exists('invoice_templates.' . $template)) {
            $template = 'invoice_templates.' . $template;
        } else {
            $template = 'templates.invoices.default';
        }

        return view($template)
            ->with('invoice', $invoice)
            ->with('logo', $invoice->companyProfile->logo())->render();
    }

    public static function quote($quote)
    {
        app()->setLocale($quote->client->language);

        config(['fi.baseCurrency' => $quote->currency_code]);

        event(new QuoteHTMLCreating($quote));

        $template = str_replace('.blade.php', '', $quote->template);

        if (view()->exists('quote_templates.' . $template)) {
            $template = 'quote_templates.' . $template;
        } else {
            $template = 'templates.quotes.default';
        }

        return view($template)
            ->with('quote', $quote)
            ->with('logo', $quote->companyProfile->logo())->render();
    }
}