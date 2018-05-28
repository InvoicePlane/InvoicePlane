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

Route::group(['middleware' => ['web', 'auth.admin'], 'prefix' => 'clients', 'namespace' => 'FI\Modules\Clients\Controllers'], function ()
{
    Route::get('/', ['uses' => 'ClientController@index', 'as' => 'clients.index']);
    Route::get('create', ['uses' => 'ClientController@create', 'as' => 'clients.create']);
    Route::get('{id}/edit', ['uses' => 'ClientController@edit', 'as' => 'clients.edit']);
    Route::get('{id}', ['uses' => 'ClientController@show', 'as' => 'clients.show']);
    Route::get('{id}/delete', ['uses' => 'ClientController@delete', 'as' => 'clients.delete']);
    Route::get('ajax/lookup', ['uses' => 'ClientController@ajaxLookup', 'as' => 'clients.ajax.lookup']);

    Route::post('create', ['uses' => 'ClientController@store', 'as' => 'clients.store']);
    Route::post('ajax/modal_edit', ['uses' => 'ClientController@ajaxModalEdit', 'as' => 'clients.ajax.modalEdit']);
    Route::post('ajax/modal_lookup', ['uses' => 'ClientController@ajaxModalLookup', 'as' => 'clients.ajax.modalLookup']);
    Route::post('ajax/modal_update/{id}', ['uses' => 'ClientController@ajaxModalUpdate', 'as' => 'clients.ajax.modalUpdate']);
    Route::post('ajax/check_name', ['uses' => 'ClientController@ajaxCheckName', 'as' => 'clients.ajax.checkName']);
    Route::post('ajax/check_duplicate_name', ['uses' => 'ClientController@ajaxCheckDuplicateName', 'as' => 'clients.ajax.checkDuplicateName']);
    Route::post('{id}/edit', ['uses' => 'ClientController@update', 'as' => 'clients.update']);

    Route::post('bulk/delete', ['uses' => 'ClientController@bulkDelete', 'as' => 'clients.bulk.delete']);

    Route::group(['prefix' => '{clientId}/contacts'], function()
    {
        Route::get('create', ['uses' => 'ContactController@create', 'as' => 'clients.contacts.create']);
        Route::post('create', ['uses' => 'ContactController@store', 'as' => 'clients.contacts.store']);
        Route::get('edit/{contactId}', ['uses' => 'ContactController@edit', 'as' => 'clients.contacts.edit']);
        Route::post('edit/{contactId}', ['uses' => 'ContactController@update', 'as' => 'clients.contacts.update']);
        Route::post('delete', ['uses' => 'ContactController@delete', 'as' => 'clients.contacts.delete']);
        Route::post('default', ['uses' => 'ContactController@updateDefault', 'as' => 'clients.contacts.updateDefault']);
    });
});