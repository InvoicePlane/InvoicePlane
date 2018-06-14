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

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'IP\Modules\TaxRates\Controllers'], function () {
    Route::get('tax_rates', ['uses' => 'TaxRateController@index', 'as' => 'taxRates.index']);
    Route::get('tax_rates/create', ['uses' => 'TaxRateController@create', 'as' => 'taxRates.create']);
    Route::get('tax_rates/{taxRate}/edit', ['uses' => 'TaxRateController@edit', 'as' => 'taxRates.edit']);
    Route::get('tax_rates/{taxRate}/delete', ['uses' => 'TaxRateController@delete', 'as' => 'taxRates.delete']);

    Route::post('tax_rates', ['uses' => 'TaxRateController@store', 'as' => 'taxRates.store']);
    Route::post('tax_rates/{taxRate}', ['uses' => 'TaxRateController@update', 'as' => 'taxRates.update']);
});