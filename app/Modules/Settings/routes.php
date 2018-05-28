<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\Settings\Controllers'], function ()
{
    Route::get('settings', ['uses' => 'SettingController@index', 'as' => 'settings.index']);
    Route::post('settings', ['uses' => 'SettingController@update', 'as' => 'settings.update']);
    Route::get('settings/update_check', ['uses' => 'SettingController@updateCheck', 'as' => 'settings.updateCheck']);
    Route::get('settings/logo/delete', ['uses' => 'SettingController@logoDelete', 'as' => 'settings.logo.delete']);
    Route::post('settings/save_tab', ['uses' => 'SettingController@saveTab', 'as' => 'settings.saveTab']);

    if (!config('app.demo'))
    {
        Route::get('backup/database', ['uses' => 'BackupController@database', 'as' => 'settings.backup.database']);
    }
});