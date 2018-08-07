<?php

namespace IP\Events\Listeners;

use IP\Events\InvoiceCreating;
use IP\Modules\Currencies\Support\CurrencyConverterFactory;
use IP\Modules\Groups\Models\Group;
use IP\Support\DateFormatter;
use IP\Support\Statuses\InvoiceStatuses;

class InvoiceCreatingListener
{
    public function handle(InvoiceCreating $event)
    {
        $invoice = $event->invoice;

        if (!$invoice->client_id) {
            // This needs to throw an exception since this is required.
        }

        if (!$invoice->user_id) {
            $invoice->user_id = auth()->user()->id;
        }

        if (!$invoice->invoice_date) {
            $invoice->invoice_date = date('Y-m-d');
        }

        if (!$invoice->due_at) {
            $invoice->due_at = DateFormatter::incrementDateByDays($invoice->invoice_date->format('Y-m-d'), config('ip.invoicesDueAfter'));
        }

        if (!$invoice->company_profile_id) {
            $invoice->company_profile_id = config('ip.defaultCompanyProfile');
        }

        if (!$invoice->group_id) {
            $invoice->group_id = config('ip.invoiceGroup');
        }

        if (!$invoice->number) {
            $invoice->number = Group::generateNumber($invoice->group_id);
        }

        if (!isset($invoice->terms)) {
            $invoice->terms = config('ip.invoiceTerms');
        }

        if (!isset($invoice->footer)) {
            $invoice->footer = config('ip.invoiceFooter');
        }

        if (!$invoice->invoice_status_id) {
            $invoice->invoice_status_id = InvoiceStatuses::getStatusId('draft');
        }

        if (!$invoice->currency_code) {
            $invoice->currency_code = $invoice->client->currency_code;
        }

        if (!$invoice->template) {
            $invoice->template = $invoice->companyProfile->invoice_template;
        }

        if ($invoice->currency_code == config('ip.baseCurrency')) {
            $invoice->exchange_rate = 1;
        } elseif (!$invoice->exchange_rate) {
            $currencyConverter = CurrencyConverterFactory::create();
            $invoice->exchange_rate = $currencyConverter->convert(config('ip.baseCurrency'), $invoice->currency_code);
        }

        $invoice->url_key = str_random(32);
    }
}
