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

Route::group(['middleware' => ['web', 'auth.admin'], 'prefix' => 'addons', 'namespace' => 'IP\Modules\Addons\Controllers'], function () {
    Route::get('/', ['uses' => 'AddonController@index', 'as' => 'addons.index']);

    Route::get('install/{id}', ['uses' => 'AddonController@install', 'as' => 'addons.install']);
    Route::get('uninstall/{id}', ['uses' => 'AddonController@uninstall', 'as' => 'addons.uninstall']);
    Route::get('upgrade/{id}', ['uses' => 'AddonController@upgrade', 'as' => 'addons.upgrade']);
});