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
    'middleware' => ['web', 'auth.admin'],
    'namespace' => 'IP\Modules\Currencies\Controllers',
], function () {
    Route::get('currencies', ['uses' => 'CurrencyController@index', 'as' => 'currencies.index']);
    Route::get('currencies/create', ['uses' => 'CurrencyController@create', 'as' => 'currencies.create']);
    Route::get('currencies/{id}/edit', ['uses' => 'CurrencyController@edit', 'as' => 'currencies.edit']);
    Route::get('currencies/{id}/delete', ['uses' => 'CurrencyController@delete', 'as' => 'currencies.delete']);

    Route::post('currencies', ['uses' => 'CurrencyController@store', 'as' => 'currencies.store']);
    Route::post('currencies/get-exchange-rate',
        ['uses' => 'CurrencyController@getExchangeRate', 'as' => 'currencies.getExchangeRate']);
    Route::post('currencies/{id}', ['uses' => 'CurrencyController@update', 'as' => 'currencies.update']);

});
