<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\CustomFields\Controllers'], function ()
{
    Route::get('custom_fields', ['uses' => 'CustomFieldController@index', 'as' => 'customFields.index']);
    Route::get('custom_fields/create', ['uses' => 'CustomFieldController@create', 'as' => 'customFields.create']);
    Route::get('custom_fields/{id}/edit', ['uses' => 'CustomFieldController@edit', 'as' => 'customFields.edit']);
    Route::get('custom_fields/{id}/delete', ['uses' => 'CustomFieldController@delete', 'as' => 'customFields.delete']);

    Route::post('custom_fields', ['uses' => 'CustomFieldController@store', 'as' => 'customFields.store']);
    Route::post('custom_fields/{id}', ['uses' => 'CustomFieldController@update', 'as' => 'customFields.update']);
});