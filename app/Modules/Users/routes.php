<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\Users\Controllers'], function ()
{
    Route::get('users', ['uses' => 'UserController@index', 'as' => 'users.index']);

    Route::get('users/create/{userType}', ['uses' => 'UserController@create', 'as' => 'users.create']);
    Route::post('users/create/{userType}', ['uses' => 'UserController@store', 'as' => 'users.store']);

    Route::get('users/{id}/edit/{userType}', ['uses' => 'UserController@edit', 'as' => 'users.edit']);
    Route::post('users/{id}/edit/{userType}', ['uses' => 'UserController@update', 'as' => 'users.update']);

    Route::get('users/{id}/delete', ['uses' => 'UserController@delete', 'as' => 'users.delete']);

    Route::get('users/{id}/password/edit', ['uses' => 'UserPasswordController@edit', 'as' => 'users.password.edit']);
    Route::post('users/{id}/password/edit', ['uses' => 'UserPasswordController@update', 'as' => 'users.password.update']);

    Route::post('users/client', ['uses' => 'UserController@getClientInfo', 'as' => 'users.clientInfo']);
});