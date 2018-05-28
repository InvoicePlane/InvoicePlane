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