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
    'middleware' => ['web', 'auth.admin'],
    'prefix' => 'export',
    'namespace' => 'IP\Modules\Exports\Controllers',
], function () {
    Route::get('/', ['uses' => 'ExportController@index', 'as' => 'export.index']);
    Route::post('{export}', ['uses' => 'ExportController@export', 'as' => 'export.export']);
});
