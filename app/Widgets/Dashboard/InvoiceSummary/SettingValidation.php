<?php

return [
    'rules' => [
        'widgetInvoiceSummaryDashboardTotalsFromDate' => 'required_if:widgetInvoiceSummaryDashboardTotals,custom_date_range',
        'widgetInvoiceSummaryDashboardTotalsToDate' => 'required_if:widgetInvoiceSummaryDashboardTotals,custom_date_range',
    ],
    'messages' => [
        'widgetInvoiceSummaryDashboardTotalsFromDate.required_if' => trans('fi.validation_invoice_summary_from_date'),
        'widgetInvoiceSummaryDashboardTotalsToDate.required_if' => trans('fi.validation_invoice_summary_to_date'),
    ],
];