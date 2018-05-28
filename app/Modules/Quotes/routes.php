<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\Quotes\Controllers'], function ()
{
    Route::group(['prefix' => 'quotes'], function ()
    {
        Route::get('/', ['uses' => 'QuoteController@index', 'as' => 'quotes.index']);
        Route::get('create', ['uses' => 'QuoteCreateController@create', 'as' => 'quotes.create']);
        Route::post('create', ['uses' => 'QuoteCreateController@store', 'as' => 'quotes.store']);
        Route::get('{id}/edit', ['uses' => 'QuoteEditController@edit', 'as' => 'quotes.edit']);
        Route::post('{id}/edit', ['uses' => 'QuoteEditController@update', 'as' => 'quotes.update']);
        Route::get('{id}/delete', ['uses' => 'QuoteController@delete', 'as' => 'quotes.delete']);
        Route::get('{id}/pdf', ['uses' => 'QuoteController@pdf', 'as' => 'quotes.pdf']);

        Route::get('{id}/edit/refresh', ['uses' => 'QuoteEditController@refreshEdit', 'as' => 'quoteEdit.refreshEdit']);
        Route::post('edit/refresh_to', ['uses' => 'QuoteEditController@refreshTo', 'as' => 'quoteEdit.refreshTo']);
        Route::post('edit/refresh_from', ['uses' => 'QuoteEditController@refreshFrom', 'as' => 'quoteEdit.refreshFrom']);
        Route::post('edit/refresh_totals', ['uses' => 'QuoteEditController@refreshTotals', 'as' => 'quoteEdit.refreshTotals']);
        Route::post('edit/update_client', ['uses' => 'QuoteEditController@updateClient', 'as' => 'quoteEdit.updateClient']);
        Route::post('edit/update_company_profile', ['uses' => 'QuoteEditController@updateCompanyProfile', 'as' => 'quoteEdit.updateCompanyProfile']);
        Route::post('recalculate', ['uses' => 'QuoteRecalculateController@recalculate', 'as' => 'quotes.recalculate']);
        Route::post('bulk/delete', ['uses' => 'QuoteController@bulkDelete', 'as' => 'quotes.bulk.delete']);
        Route::post('bulk/status', ['uses' => 'QuoteController@bulkStatus', 'as' => 'quotes.bulk.status']);
    });

    Route::group(['prefix' => 'quote_copy'], function ()
    {
        Route::post('create', ['uses' => 'QuoteCopyController@create', 'as' => 'quoteCopy.create']);
        Route::post('store', ['uses' => 'QuoteCopyController@store', 'as' => 'quoteCopy.store']);
    });

    Route::group(['prefix' => 'quote_to_invoice'], function ()
    {
        Route::post('create', ['uses' => 'QuoteToInvoiceController@create', 'as' => 'quoteToInvoice.create']);
        Route::post('store', ['uses' => 'QuoteToInvoiceController@store', 'as' => 'quoteToInvoice.store']);
    });

    Route::group(['prefix' => 'quote_mail'], function ()
    {
        Route::post('create', ['uses' => 'QuoteMailController@create', 'as' => 'quoteMail.create']);
        Route::post('store', ['uses' => 'QuoteMailController@store', 'as' => 'quoteMail.store']);
    });

    Route::group(['prefix' => 'quote_item'], function ()
    {
        Route::post('delete', ['uses' => 'QuoteItemController@delete', 'as' => 'quoteItem.delete']);
    });
});