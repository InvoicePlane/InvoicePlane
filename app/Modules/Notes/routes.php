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

Route::group([
    'prefix' => 'notes',
    'middleware' => ['web', 'auth'],
    'namespace' => 'IP\Modules\Notes\Controllers',
], function () {
    Route::post('create', ['uses' => 'NoteController@create', 'as' => 'notes.create']);
    Route::post('delete', ['uses' => 'NoteController@delete', 'as' => 'notes.delete']);
});
