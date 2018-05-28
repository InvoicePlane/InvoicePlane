<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => 'web', 'namespace' => 'FI\Modules\Setup\Controllers'], function ()
{
    Route::get('setup', ['uses' => 'SetupController@index', 'as' => 'setup.index']);
    Route::post('setup', ['uses' => 'SetupController@postIndex', 'as' => 'setup.postIndex']);

    Route::get('setup/prerequisites', ['uses' => 'SetupController@prerequisites', 'as' => 'setup.prerequisites']);

    Route::get('setup/migration', ['uses' => 'SetupController@migration', 'as' => 'setup.migration']);
    Route::post('setup/migration', ['uses' => 'SetupController@postMigration', 'as' => 'setup.postMigration']);

    Route::get('setup/account', ['uses' => 'SetupController@account', 'as' => 'setup.account']);
    Route::post('setup/account', ['uses' => 'SetupController@postAccount', 'as' => 'setup.postAccount']);

    Route::get('setup/complete', ['uses' => 'SetupController@complete', 'as' => 'setup.complete']);
});