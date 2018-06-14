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

Route::group(['namespace' => 'IP\Modules\Sessions\Controllers', 'middleware' => 'web'], function () {
    Route::get('login', ['uses' => 'SessionController@login', 'as' => 'session.login']);
    Route::post('login', ['uses' => 'SessionController@attempt', 'as' => 'session.attempt']);
    Route::get('logout', ['uses' => 'SessionController@logout', 'as' => 'session.logout']);
});