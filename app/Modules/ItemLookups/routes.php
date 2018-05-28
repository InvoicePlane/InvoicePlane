<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\ItemLookups\Controllers'], function ()
{
    Route::get('item_lookups', ['uses' => 'ItemLookupController@index', 'as' => 'itemLookups.index']);
    Route::get('item_lookups/create', ['uses' => 'ItemLookupController@create', 'as' => 'itemLookups.create']);
    Route::get('item_lookups/{itemLookup}/edit', ['uses' => 'ItemLookupController@edit', 'as' => 'itemLookups.edit']);
    Route::get('item_lookups/{itemLookup}/delete', ['uses' => 'ItemLookupController@delete', 'as' => 'itemLookups.delete']);
    Route::get('item_lookups/ajax/item_lookup', ['uses' => 'ItemLookupController@ajaxItemLookup', 'as' => 'itemLookups.ajax.itemLookup']);

    Route::post('item_lookups', ['uses' => 'ItemLookupController@store', 'as' => 'itemLookups.store']);
    Route::post('item_lookups/{itemLookup}', ['uses' => 'ItemLookupController@update', 'as' => 'itemLookups.update']);
    Route::post('item_lookups/ajax/process', ['uses' => 'ItemLookupController@process', 'as' => 'itemLookups.ajax.process']);
});