<?php

/**
 * InvoicePlane
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (C) 2014 - 2018 InvoicePlane
 * @license     https://invoiceplane.com/license
 * @link        https://invoiceplane.com
 *
 * Based on FusionInvoice by Jesse Terry (FusionInvoice, LLC)
 */

Route::group(['prefix' => 'api', 'middleware' => 'web', 'namespace' => 'FI\Modules\API\Controllers'], function () {
    Route::group(['middleware' => 'auth.admin'], function () {
        Route::post('generate_keys', ['uses' => 'ApiKeyController@generateKeys', 'as' => 'api.generateKeys']);
    });

    Route::group(['middleware' => 'auth.api'], function () {
        Route::post('clients/list', ['uses' => 'ApiClientController@lists']);
        Route::post('clients/show', ['uses' => 'ApiClientController@show']);
        Route::post('clients/store', ['uses' => 'ApiClientController@store']);
        Route::post('clients/update', ['uses' => 'ApiClientController@update']);
        Route::post('clients/delete', ['uses' => 'ApiClientController@delete']);

        Route::post('quotes/list', ['uses' => 'ApiQuoteController@lists']);
        Route::post('quotes/show', ['uses' => 'ApiQuoteController@show']);
        Route::post('quotes/store', ['uses' => 'ApiQuoteController@store']);
        Route::post('quotes/items/add', ['uses' => 'ApiQuoteController@addItem']);
        Route::post('quotes/delete', ['uses' => 'ApiQuoteController@delete']);

        Route::post('invoices/list', ['uses' => 'ApiInvoiceController@lists']);
        Route::post('invoices/show', ['uses' => 'ApiInvoiceController@show']);
        Route::post('invoices/store', ['uses' => 'ApiInvoiceController@store']);
        Route::post('invoices/items/add', ['uses' => 'ApiInvoiceController@addItem']);
        Route::post('invoices/delete', ['uses' => 'ApiInvoiceController@delete']);

        Route::post('payments/list', ['uses' => 'ApiPaymentController@lists']);
        Route::post('payments/show', ['uses' => 'ApiPaymentController@show']);
        Route::post('payments/store', ['uses' => 'ApiPaymentController@store']);
        Route::post('payments/items/add', ['uses' => 'ApiPaymentController@addItem']);
        Route::post('payments/delete', ['uses' => 'ApiPaymentController@delete']);
    });

});