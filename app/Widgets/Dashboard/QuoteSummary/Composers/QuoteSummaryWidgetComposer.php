<?php

namespace FI\Widgets\Dashboard\QuoteSummary\Composers;

use FI\Modules\Quotes\Models\QuoteAmount;
use FI\Support\CurrencyFormatter;
use Illuminate\Support\Facades\DB;

class QuoteSummaryWidgetComposer
{
    public function compose($view)
    {
        $view->with('quotesTotalDraft', $this->getQuoteTotalDraft())
            ->with('quotesTotalSent', $this->getQuoteTotalSent())
            ->with('quotesTotalApproved', $this->getQuoteTotalApproved())
            ->with('quotesTotalRejected', $this->getQuoteTotalRejected())
            ->with('quoteDashboardTotalOptions', periods());
    }

    private function getQuoteTotalDraft()
    {
        return CurrencyFormatter::format(QuoteAmount::join('quotes', 'quotes.id', '=', 'quote_amounts.quote_id')
            ->whereHas('quote', function ($q) {
                $q->draft();
                $q->where('invoice_id', 0);
                switch (config('fi.widgetQuoteSummaryDashboardTotals')) {
                    case 'year_to_date':
                        $q->yearToDate();
                        break;
                    case 'this_quarter':
                        $q->thisQuarter();
                        break;
                    case 'custom_date_range':
                        $q->dateRange(config('fi.widgetQuoteSummaryDashboardTotalsFromDate'), config('fi.widgetQuoteSummaryDashboardTotalsToDate'));
                        break;
                }
            })->sum(DB::raw('total / exchange_rate')));
    }

    private function getQuoteTotalSent()
    {
        return CurrencyFormatter::format(QuoteAmount::join('quotes', 'quotes.id', '=', 'quote_amounts.quote_id')
            ->whereHas('quote', function ($q) {
                $q->sent();
                $q->where('invoice_id', 0);
                switch (config('fi.widgetQuoteSummaryDashboardTotals')) {
                    case 'year_to_date':
                        $q->yearToDate();
                        break;
                    case 'this_quarter':
                        $q->thisQuarter();
                        break;
                    case 'custom_date_range':
                        $q->dateRange(config('fi.widgetQuoteSummaryDashboardTotalsFromDate'), config('fi.widgetQuoteSummaryDashboardTotalsToDate'));
                        break;
                }
            })->sum(DB::raw('total / exchange_rate')));
    }

    private function getQuoteTotalApproved()
    {
        return CurrencyFormatter::format(QuoteAmount::join('quotes', 'quotes.id', '=', 'quote_amounts.quote_id')
            ->whereHas('quote', function ($q) {
                $q->approved();
                $q->where('invoice_id', 0);
                switch (config('fi.widgetQuoteSummaryDashboardTotals')) {
                    case 'year_to_date':
                        $q->yearToDate();
                        break;
                    case 'this_quarter':
                        $q->thisQuarter();
                        break;
                    case 'custom_date_range':
                        $q->dateRange(config('fi.widgetQuoteSummaryDashboardTotalsFromDate'), config('fi.widgetQuoteSummaryDashboardTotalsToDate'));
                        break;
                }
            })->sum(DB::raw('total / exchange_rate')));
    }

    private function getQuoteTotalRejected()
    {
        return CurrencyFormatter::format(QuoteAmount::join('quotes', 'quotes.id', '=', 'quote_amounts.quote_id')
            ->whereHas('quote', function ($q) {
                $q->rejected();
                $q->where('invoice_id', 0);
                switch (config('fi.widgetQuoteSummaryDashboardTotals')) {
                    case 'year_to_date':
                        $q->yearToDate();
                        break;
                    case 'this_quarter':
                        $q->thisQuarter();
                        break;
                    case 'custom_date_range':
                        $q->dateRange(config('fi.widgetQuoteSummaryDashboardTotalsFromDate'), config('fi.widgetQuoteSummaryDashboardTotalsToDate'));
                        break;
                }
            })->sum(DB::raw('total / exchange_rate')));
    }
}