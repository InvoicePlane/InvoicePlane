<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['prefix' => 'mail_log', 'middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\MailQueue\Controllers'], function ()
{
    Route::get('/', ['uses' => 'MailLogController@index', 'as' => 'mailLog.index']);
    Route::post('content', ['uses' => 'MailLogController@content', 'as' => 'mailLog.content']);
    Route::get('{id}/delete', ['uses' => 'MailLogController@delete', 'as' => 'mailLog.delete']);
});