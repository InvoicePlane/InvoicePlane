<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'prefix' => 'export', 'namespace' => 'FI\Modules\Exports\Controllers'], function ()
{
    Route::get('/', ['uses' => 'ExportController@index', 'as' => 'export.index']);
    Route::post('{export}', ['uses' => 'ExportController@export', 'as' => 'export.export']);
});