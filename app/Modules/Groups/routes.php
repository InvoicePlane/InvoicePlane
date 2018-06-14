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

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'IP\Modules\Groups\Controllers'], function () {
    Route::get('groups', ['uses' => 'GroupController@index', 'as' => 'groups.index']);
    Route::get('groups/create', ['uses' => 'GroupController@create', 'as' => 'groups.create']);
    Route::get('groups/{group}/edit', ['uses' => 'GroupController@edit', 'as' => 'groups.edit']);
    Route::get('groups/{group}/delete', ['uses' => 'GroupController@delete', 'as' => 'groups.delete']);

    Route::post('groups', ['uses' => 'GroupController@store', 'as' => 'groups.store']);
    Route::post('groups/{group}', ['uses' => 'GroupController@update', 'as' => 'groups.update']);
});