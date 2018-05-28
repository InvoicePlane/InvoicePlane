<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\RecurringInvoices\Controllers'], function ()
{
    Route::group(['prefix' => 'recurring_invoices'], function ()
    {
        Route::get('/', ['uses' => 'RecurringInvoiceController@index', 'as' => 'recurringInvoices.index']);
        Route::get('create', ['uses' => 'RecurringInvoiceCreateController@create', 'as' => 'recurringInvoices.create']);
        Route::post('create', ['uses' => 'RecurringInvoiceCreateController@store', 'as' => 'recurringInvoices.store']);
        Route::get('{id}/edit', ['uses' => 'RecurringInvoiceEditController@edit', 'as' => 'recurringInvoices.edit']);
        Route::post('{id}/edit', ['uses' => 'RecurringInvoiceEditController@update', 'as' => 'recurringInvoices.update']);
        Route::get('{id}/delete', ['uses' => 'RecurringInvoiceController@delete', 'as' => 'recurringInvoices.delete']);

        Route::get('{id}/edit/refresh', ['uses' => 'RecurringInvoiceEditController@refreshEdit', 'as' => 'recurringInvoiceEdit.refreshEdit']);
        Route::post('edit/refresh_to', ['uses' => 'RecurringInvoiceEditController@refreshTo', 'as' => 'recurringInvoiceEdit.refreshTo']);
        Route::post('edit/refresh_from', ['uses' => 'RecurringInvoiceEditController@refreshFrom', 'as' => 'recurringInvoiceEdit.refreshFrom']);
        Route::post('edit/refresh_totals', ['uses' => 'RecurringInvoiceEditController@refreshTotals', 'as' => 'recurringInvoiceEdit.refreshTotals']);
        Route::post('edit/update_client', ['uses' => 'RecurringInvoiceEditController@updateClient', 'as' => 'recurringInvoiceEdit.updateClient']);
        Route::post('edit/update_company_profile', ['uses' => 'RecurringInvoiceEditController@updateCompanyProfile', 'as' => 'recurringInvoiceEdit.updateCompanyProfile']);
        Route::post('recalculate', ['uses' => 'RecurringInvoiceRecalculateController@recalculate', 'as' => 'recurringInvoices.recalculate']);
    });

    Route::group(['prefix' => 'recurring_invoice_copy'], function ()
    {
        Route::post('create', ['uses' => 'RecurringInvoiceCopyController@create', 'as' => 'recurringInvoiceCopy.create']);
        Route::post('store', ['uses' => 'RecurringInvoiceCopyController@store', 'as' => 'recurringInvoiceCopy.store']);
    });

    Route::group(['prefix' => 'recurring_invoice_item'], function ()
    {
        Route::post('delete', ['uses' => 'RecurringInvoiceItemController@delete', 'as' => 'recurringInvoiceItem.delete']);
    });
});