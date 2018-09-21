<?php

namespace IP\Events\Listeners;

use IP\Events\RecurringInvoiceCreating;
use IP\Modules\Currencies\Support\CurrencyConverterFactory;

class RecurringInvoiceCreatingListener
{
    public function handle(RecurringInvoiceCreating $event)
    {
        $recurringInvoice = $event->recurringInvoice;

        if (!$recurringInvoice->user_id) {
            $recurringInvoice->user_id = auth()->user()->id;
        }

        if (!$recurringInvoice->company_profile_id) {
            $recurringInvoice->company_profile_id = config('ip.defaultCompanyProfile');
        }

        if (!$recurringInvoice->group_id) {
            $recurringInvoice->group_id = config('ip.invoiceGroup');
        }

        if (!isset($recurringInvoice->terms)) {
            $recurringInvoice->terms = config('ip.invoiceTerms');
        }

        if (!isset($recurringInvoice->footer)) {
            $recurringInvoice->footer = config('ip.invoiceFooter');
        }

        if (!$recurringInvoice->template) {
            $recurringInvoice->template = $recurringInvoice->companyProfile->invoice_template;
        }

        if (!$recurringInvoice->currency_code) {
            $recurringInvoice->currency_code = $recurringInvoice->client->currency_code;
        }

        if ($recurringInvoice->currency_code == config('ip.baseCurrency')) {
            $recurringInvoice->exchange_rate = 1;
        } elseif (!$recurringInvoice->exchange_rate) {
            $currencyConverter = CurrencyConverterFactory::create();
            $recurringInvoice->exchange_rate = $currencyConverter->convert(config('ip.baseCurrency'), $recurringInvoice->currency_code);
        }
    }
}
