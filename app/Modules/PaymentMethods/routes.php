<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\PaymentMethods\Controllers'], function ()
{
    Route::get('payment_methods', ['uses' => 'PaymentMethodController@index', 'as' => 'paymentMethods.index']);
    Route::get('payment_methods/create', ['uses' => 'PaymentMethodController@create', 'as' => 'paymentMethods.create']);
    Route::get('payment_methods/{paymentMethod}/edit', ['uses' => 'PaymentMethodController@edit', 'as' => 'paymentMethods.edit']);
    Route::get('payment_methods/{paymentMethod}/delete', ['uses' => 'PaymentMethodController@delete', 'as' => 'paymentMethods.delete']);

    Route::post('payment_methods', ['uses' => 'PaymentMethodController@store', 'as' => 'paymentMethods.store']);
    Route::post('payment_methods/{paymentMethod}', ['uses' => 'PaymentMethodController@update', 'as' => 'paymentMethods.update']);
});