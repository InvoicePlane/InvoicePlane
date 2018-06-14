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

Route::group(['middleware' => 'web', 'namespace' => 'IP\Modules\Setup\Controllers'], function () {
    Route::get('setup', ['uses' => 'SetupController@index', 'as' => 'setup.index']);
    Route::post('setup', ['uses' => 'SetupController@postIndex', 'as' => 'setup.postIndex']);

    Route::get('setup/prerequisites', ['uses' => 'SetupController@prerequisites', 'as' => 'setup.prerequisites']);

    Route::get('setup/db-config', ['uses' => 'SetupController@dbConfig', 'as' => 'setup.dbconfig']);
    Route::post('setup/db-config', ['uses' => 'SetupController@postDbConfig', 'as' => 'setup.postDbconfig']);

    Route::get('setup/migration', ['uses' => 'SetupController@migration', 'as' => 'setup.migration']);
    Route::post('setup/migration', ['uses' => 'SetupController@postMigration', 'as' => 'setup.postMigration']);

    Route::get('setup/account', ['uses' => 'SetupController@account', 'as' => 'setup.account']);
    Route::post('setup/account', ['uses' => 'SetupController@postAccount', 'as' => 'setup.postAccount']);

    Route::get('setup/complete', ['uses' => 'SetupController@complete', 'as' => 'setup.complete']);
});