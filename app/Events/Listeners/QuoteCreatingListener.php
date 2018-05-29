<?php

namespace FI\Events\Listeners;

use FI\Events\QuoteCreating;
use FI\Modules\Currencies\Support\CurrencyConverterFactory;
use FI\Modules\Groups\Models\Group;
use FI\Support\DateFormatter;
use FI\Support\Statuses\QuoteStatuses;

class QuoteCreatingListener
{
    public function handle(QuoteCreating $event)
    {
        $quote = $event->quote;

        if (!$quote->client_id) {
            // This needs to throw an exception since this is required.
        }

        if (!$quote->user_id) {
            $quote->user_id = auth()->user()->id;
        }

        if (!$quote->quote_date) {
            $quote->quote_date = date('Y-m-d');
        }

        if (!$quote->expires_at) {
            $quote->expires_at = DateFormatter::incrementDateByDays($quote->quote_date->format('Y-m-d'), config('fi.quotesExpireAfter'));
        }

        if (!$quote->company_profile_id) {
            $quote->company_profile_id = config('fi.defaultCompanyProfile');
        }

        if (!$quote->group_id) {
            $quote->group_id = config('fi.quoteGroup');
        }

        if (!$quote->number) {
            $quote->number = Group::generateNumber($quote->group_id);
        }

        if (!isset($quote->terms)) {
            $quote->terms = config('fi.quoteTerms');
        }

        if (!isset($quote->footer)) {
            $quote->footer = config('fi.quoteFooter');
        }

        if (!$quote->quote_status_id) {
            $quote->quote_status_id = QuoteStatuses::getStatusId('draft');
        }

        if (!$quote->currency_code) {
            $quote->currency_code = $quote->client->currency_code;
        }

        if (!$quote->template) {
            $quote->template = $quote->companyProfile->quote_template;
        }

        if ($quote->currency_code == config('fi.baseCurrency')) {
            $quote->exchange_rate = 1;
        } elseif (!$quote->exchange_rate) {
            $currencyConverter = CurrencyConverterFactory::create();
            $quote->exchange_rate = $currencyConverter->convert(config('fi.baseCurrency'), $quote->currency_code);
        }

        $quote->url_key = str_random(32);
    }
}
