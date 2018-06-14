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

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'IP\Modules\PaymentMethods\Controllers'], function () {
    Route::get('payment_methods', ['uses' => 'PaymentMethodController@index', 'as' => 'paymentMethods.index']);
    Route::get('payment_methods/create', ['uses' => 'PaymentMethodController@create', 'as' => 'paymentMethods.create']);
    Route::get('payment_methods/{paymentMethod}/edit', ['uses' => 'PaymentMethodController@edit', 'as' => 'paymentMethods.edit']);
    Route::get('payment_methods/{paymentMethod}/delete', ['uses' => 'PaymentMethodController@delete', 'as' => 'paymentMethods.delete']);

    Route::post('payment_methods', ['uses' => 'PaymentMethodController@store', 'as' => 'paymentMethods.store']);
    Route::post('payment_methods/{paymentMethod}', ['uses' => 'PaymentMethodController@update', 'as' => 'paymentMethods.update']);
});