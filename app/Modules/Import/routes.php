<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\Import\Controllers'], function ()
{
    Route::get('import', ['uses' => 'ImportController@index', 'as' => 'import.index']);
    Route::get('import/map/{import_type}', ['uses' => 'ImportController@mapImport', 'as' => 'import.map']);

    Route::post('import/upload', ['uses' => 'ImportController@upload', 'as' => 'import.upload']);
    Route::post('import/map/{import_type}', ['uses' => 'ImportController@mapImportSubmit', 'as' => 'import.map.submit']);
});