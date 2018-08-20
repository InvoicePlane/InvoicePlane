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

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'IP\Modules\Users\Controllers'], function () {
    Route::get('users', ['uses' => 'UserController@index', 'as' => 'users.index']);

    Route::get('users/create/{userType}', ['uses' => 'UserController@create', 'as' => 'users.create']);
    Route::post('users/create/{userType}', ['uses' => 'UserController@store', 'as' => 'users.store']);

    Route::get('users/{id}/edit/{userType}', ['uses' => 'UserController@edit', 'as' => 'users.edit']);
    Route::post('users/{id}/edit/{userType}', ['uses' => 'UserController@update', 'as' => 'users.update']);

    Route::get('users/{id}/delete', ['uses' => 'UserController@delete', 'as' => 'users.delete']);

    Route::get('users/{id}/password/edit', ['uses' => 'UserPasswordController@edit', 'as' => 'users.password.edit']);
    Route::post('users/{id}/password/edit',
        ['uses' => 'UserPasswordController@update', 'as' => 'users.password.update']);

    Route::post('users/client', ['uses' => 'UserController@getClientInfo', 'as' => 'users.clientInfo']);
});
