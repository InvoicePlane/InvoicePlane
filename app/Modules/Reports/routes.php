<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['prefix' => 'report', 'middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\Reports\Controllers'], function ()
{
    Route::get('client_statement', ['uses' => 'ClientStatementReportController@index', 'as' => 'reports.clientStatement']);
    Route::post('client_statement/validate', ['uses' => 'ClientStatementReportController@validateOptions', 'as' => 'reports.clientStatement.validate']);
    Route::get('client_statement/html', ['uses' => 'ClientStatementReportController@html', 'as' => 'reports.clientStatement.html']);
    Route::get('client_statement/pdf', ['uses' => 'ClientStatementReportController@pdf', 'as' => 'reports.clientStatement.pdf']);

    Route::get('item_sales', ['uses' => 'ItemSalesReportController@index', 'as' => 'reports.itemSales']);
    Route::post('item_sales/validate', ['uses' => 'ItemSalesReportController@validateOptions', 'as' => 'reports.itemSales.validate']);
    Route::get('item_sales/html', ['uses' => 'ItemSalesReportController@html', 'as' => 'reports.itemSales.html']);
    Route::get('item_sales/pdf', ['uses' => 'ItemSalesReportController@pdf', 'as' => 'reports.itemSales.pdf']);

    Route::get('payments_collected', ['uses' => 'PaymentsCollectedReportController@index', 'as' => 'reports.paymentsCollected']);
    Route::post('payments_collected/validate', ['uses' => 'PaymentsCollectedReportController@validateOptions', 'as' => 'reports.paymentsCollected.validate']);
    Route::get('payments_collected/html', ['uses' => 'PaymentsCollectedReportController@html', 'as' => 'reports.paymentsCollected.html']);
    Route::get('payments_collected/pdf', ['uses' => 'PaymentsCollectedReportController@pdf', 'as' => 'reports.paymentsCollected.pdf']);

    Route::get('revenue_by_client', ['uses' => 'RevenueByClientReportController@index', 'as' => 'reports.revenueByClient']);
    Route::post('revenue_by_client/validate', ['uses' => 'RevenueByClientReportController@validateOptions', 'as' => 'reports.revenueByClient.validate']);
    Route::get('revenue_by_client/html', ['uses' => 'RevenueByClientReportController@html', 'as' => 'reports.revenueByClient.html']);
    Route::get('revenue_by_client/pdf', ['uses' => 'RevenueByClientReportController@pdf', 'as' => 'reports.revenueByClient.pdf']);

    Route::get('tax_summary', ['uses' => 'TaxSummaryReportController@index', 'as' => 'reports.taxSummary']);
    Route::post('tax_summary/validate', ['uses' => 'TaxSummaryReportController@validateOptions', 'as' => 'reports.taxSummary.validate']);
    Route::get('tax_summary/html', ['uses' => 'TaxSummaryReportController@html', 'as' => 'reports.taxSummary.html']);
    Route::get('tax_summary/pdf', ['uses' => 'TaxSummaryReportController@pdf', 'as' => 'reports.taxSummary.pdf']);

    Route::get('profit_loss', ['uses' => 'ProfitLossReportController@index', 'as' => 'reports.profitLoss']);
    Route::post('profit_loss/validate', ['uses' => 'ProfitLossReportController@validateOptions', 'as' => 'reports.profitLoss.validate']);
    Route::get('profit_loss/html', ['uses' => 'ProfitLossReportController@html', 'as' => 'reports.profitLoss.html']);
    Route::get('profit_loss/pdf', ['uses' => 'ProfitLossReportController@pdf', 'as' => 'reports.profitLoss.pdf']);

    Route::get('expense_list', ['uses' => 'ExpenseListReportController@index', 'as' => 'reports.expenseList']);
    Route::post('expense_list/validate', ['uses' => 'ExpenseListReportController@validateOptions', 'as' => 'reports.expenseList.validate']);
    Route::get('expense_list/html', ['uses' => 'ExpenseListReportController@html', 'as' => 'reports.expenseList.html']);
    Route::get('expense_list/pdf', ['uses' => 'ExpenseListReportController@pdf', 'as' => 'reports.expenseList.pdf']);
});