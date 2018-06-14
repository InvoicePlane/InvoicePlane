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

Route::group(['prefix' => 'mail_log', 'middleware' => ['web', 'auth.admin'], 'namespace' => 'IP\Modules\MailQueue\Controllers'], function () {
    Route::get('/', ['uses' => 'MailLogController@index', 'as' => 'mailLog.index']);
    Route::post('content', ['uses' => 'MailLogController@content', 'as' => 'mailLog.content']);
    Route::get('{id}/delete', ['uses' => 'MailLogController@delete', 'as' => 'mailLog.delete']);
});