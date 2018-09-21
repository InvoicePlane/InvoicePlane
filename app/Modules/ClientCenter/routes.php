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

Route::group([
    'prefix' => 'client_center',
    'middleware' => 'web',
    'namespace' => 'IP\Modules\ClientCenter\Controllers',
], function () {
    Route::get('/', ['uses' => 'ClientCenterDashboardController@redirectToLogin']);
    Route::get('invoice/{invoiceKey}',
        ['uses' => 'ClientCenterPublicInvoiceController@show', 'as' => 'clientCenter.public.invoice.show']);
    Route::get('invoice/{invoiceKey}/pdf',
        ['uses' => 'ClientCenterPublicInvoiceController@pdf', 'as' => 'clientCenter.public.invoice.pdf']);
    Route::get('invoice/{invoiceKey}/html',
        ['uses' => 'ClientCenterPublicInvoiceController@html', 'as' => 'clientCenter.public.invoice.html']);
    Route::get('quote/{quoteKey}',
        ['uses' => 'ClientCenterPublicQuoteController@show', 'as' => 'clientCenter.public.quote.show']);
    Route::get('quote/{quoteKey}/pdf',
        ['uses' => 'ClientCenterPublicQuoteController@pdf', 'as' => 'clientCenter.public.quote.pdf']);
    Route::get('quote/{quoteKey}/html',
        ['uses' => 'ClientCenterPublicQuoteController@html', 'as' => 'clientCenter.public.quote.html']);
    Route::get('quote/{quoteKey}/approve',
        ['uses' => 'ClientCenterPublicQuoteController@approve', 'as' => 'clientCenter.public.quote.approve']);
    Route::get('quote/{quoteKey}/reject',
        ['uses' => 'ClientCenterPublicQuoteController@reject', 'as' => 'clientCenter.public.quote.reject']);

    Route::group(['middleware' => 'auth.clientCenter'], function () {
        Route::get('dashboard',
            ['uses' => 'ClientCenterDashboardController@index', 'as' => 'clientCenter.dashboard']);
        Route::get('invoices', ['uses' => 'ClientCenterInvoiceController@index', 'as' => 'clientCenter.invoices']);
        Route::get('quotes', ['uses' => 'ClientCenterQuoteController@index', 'as' => 'clientCenter.quotes']);
        Route::get('payments', ['uses' => 'ClientCenterPaymentController@index', 'as' => 'clientCenter.payments']);
    });
});
