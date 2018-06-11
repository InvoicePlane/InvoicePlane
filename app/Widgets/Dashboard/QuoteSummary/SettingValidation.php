<?php

return [
    'rules' => [
        'widgetQuoteSummaryDashboardTotalsFromDate' => 'required_if:widgetQuoteSummaryDashboardTotals,custom_date_range',
        'widgetQuoteSummaryDashboardTotalsToDate' => 'required_if:widgetQuoteSummaryDashboardTotals,custom_date_range',
    ],
    'messages' => [
        'widgetQuoteSummaryDashboardTotalsFromDate.required_if' => trans('ip.validation_quote_summary_from_date'),
        'widgetQuoteSummaryDashboardTotalsToDate.required_if' => trans('ip.validation_quote_summary_to_date'),
    ],
];