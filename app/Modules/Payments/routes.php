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

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\Payments\Controllers'], function ()
{
    Route::get('payments', ['uses' => 'PaymentController@index', 'as' => 'payments.index']);
    Route::post('payments/create', ['uses' => 'PaymentController@create', 'as' => 'payments.create']);
    Route::post('payments/store', ['uses' => 'PaymentController@store', 'as' => 'payments.store']);
    Route::get('payments/{payment}', ['uses' => 'PaymentController@edit', 'as' => 'payments.edit']);
    Route::post('payments/{payment}', ['uses' => 'PaymentController@update', 'as' => 'payments.update']);

    Route::get('payments/{payment}/delete', ['uses' => 'PaymentController@delete', 'as' => 'payments.delete']);

    Route::post('bulk/delete', ['uses' => 'PaymentController@bulkDelete', 'as' => 'payments.bulk.delete']);

    Route::group(['prefix' => 'payment_mail'], function ()
    {
        Route::post('create', ['uses' => 'PaymentMailController@create', 'as' => 'paymentMail.create']);
        Route::post('store', ['uses' => 'PaymentMailController@store', 'as' => 'paymentMail.store']);
    });
});