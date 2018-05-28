<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\Groups\Controllers'], function ()
{
    Route::get('groups', ['uses' => 'GroupController@index', 'as' => 'groups.index']);
    Route::get('groups/create', ['uses' => 'GroupController@create', 'as' => 'groups.create']);
    Route::get('groups/{group}/edit', ['uses' => 'GroupController@edit', 'as' => 'groups.edit']);
    Route::get('groups/{group}/delete', ['uses' => 'GroupController@delete', 'as' => 'groups.delete']);

    Route::post('groups', ['uses' => 'GroupController@store', 'as' => 'groups.store']);
    Route::post('groups/{group}', ['uses' => 'GroupController@update', 'as' => 'groups.update']);
});