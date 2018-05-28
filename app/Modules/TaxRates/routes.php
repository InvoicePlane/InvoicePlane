<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\TaxRates\Controllers'], function ()
{
    Route::get('tax_rates', ['uses' => 'TaxRateController@index', 'as' => 'taxRates.index']);
    Route::get('tax_rates/create', ['uses' => 'TaxRateController@create', 'as' => 'taxRates.create']);
    Route::get('tax_rates/{taxRate}/edit', ['uses' => 'TaxRateController@edit', 'as' => 'taxRates.edit']);
    Route::get('tax_rates/{taxRate}/delete', ['uses' => 'TaxRateController@delete', 'as' => 'taxRates.delete']);

    Route::post('tax_rates', ['uses' => 'TaxRateController@store', 'as' => 'taxRates.store']);
    Route::post('tax_rates/{taxRate}', ['uses' => 'TaxRateController@update', 'as' => 'taxRates.update']);
});