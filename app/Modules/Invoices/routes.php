<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\Invoices\Controllers'], function ()
{
    Route::group(['prefix' => 'invoices'], function ()
    {
        Route::get('/', ['uses' => 'InvoiceController@index', 'as' => 'invoices.index']);
        Route::get('create', ['uses' => 'InvoiceCreateController@create', 'as' => 'invoices.create']);
        Route::post('create', ['uses' => 'InvoiceCreateController@store', 'as' => 'invoices.store']);
        Route::get('{id}/edit', ['uses' => 'InvoiceEditController@edit', 'as' => 'invoices.edit']);
        Route::post('{id}/edit', ['uses' => 'InvoiceEditController@update', 'as' => 'invoices.update']);
        Route::get('{id}/delete', ['uses' => 'InvoiceController@delete', 'as' => 'invoices.delete']);
        Route::get('{id}/pdf', ['uses' => 'InvoiceController@pdf', 'as' => 'invoices.pdf']);

        Route::get('{id}/edit/refresh', ['uses' => 'InvoiceEditController@refreshEdit', 'as' => 'invoiceEdit.refreshEdit']);
        Route::post('edit/refresh_to', ['uses' => 'InvoiceEditController@refreshTo', 'as' => 'invoiceEdit.refreshTo']);
        Route::post('edit/refresh_from', ['uses' => 'InvoiceEditController@refreshFrom', 'as' => 'invoiceEdit.refreshFrom']);
        Route::post('edit/refresh_totals', ['uses' => 'InvoiceEditController@refreshTotals', 'as' => 'invoiceEdit.refreshTotals']);
        Route::post('edit/update_client', ['uses' => 'InvoiceEditController@updateClient', 'as' => 'invoiceEdit.updateClient']);
        Route::post('edit/update_company_profile', ['uses' => 'InvoiceEditController@updateCompanyProfile', 'as' => 'invoiceEdit.updateCompanyProfile']);
        Route::post('recalculate', ['uses' => 'InvoiceRecalculateController@recalculate', 'as' => 'invoices.recalculate']);
        Route::post('bulk/delete', ['uses' => 'InvoiceController@bulkDelete', 'as' => 'invoices.bulk.delete']);
        Route::post('bulk/status', ['uses' => 'InvoiceController@bulkStatus', 'as' => 'invoices.bulk.status']);
    });

    Route::group(['prefix' => 'invoice_copy'], function ()
    {
        Route::post('create', ['uses' => 'InvoiceCopyController@create', 'as' => 'invoiceCopy.create']);
        Route::post('store', ['uses' => 'InvoiceCopyController@store', 'as' => 'invoiceCopy.store']);
    });

    Route::group(['prefix' => 'invoice_mail'], function ()
    {
        Route::post('create', ['uses' => 'InvoiceMailController@create', 'as' => 'invoiceMail.create']);
        Route::post('store', ['uses' => 'InvoiceMailController@store', 'as' => 'invoiceMail.store']);
    });

    Route::group(['prefix' => 'invoice_item'], function ()
    {
        Route::post('delete', ['uses' => 'InvoiceItemController@delete', 'as' => 'invoiceItem.delete']);
    });
});