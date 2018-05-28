<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\Currencies\Controllers'], function ()
{
    Route::get('currencies', ['uses' => 'CurrencyController@index', 'as' => 'currencies.index']);
    Route::get('currencies/create', ['uses' => 'CurrencyController@create', 'as' => 'currencies.create']);
    Route::get('currencies/{id}/edit', ['uses' => 'CurrencyController@edit', 'as' => 'currencies.edit']);
    Route::get('currencies/{id}/delete', ['uses' => 'CurrencyController@delete', 'as' => 'currencies.delete']);

    Route::post('currencies', ['uses' => 'CurrencyController@store', 'as' => 'currencies.store']);
    Route::post('currencies/get-exchange-rate', ['uses' => 'CurrencyController@getExchangeRate', 'as' => 'currencies.getExchangeRate']);
    Route::post('currencies/{id}', ['uses' => 'CurrencyController@update', 'as' => 'currencies.update']);

});