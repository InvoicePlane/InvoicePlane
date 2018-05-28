<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['prefix' => 'notes', 'middleware' => ['web', 'auth'], 'namespace' => 'FI\Modules\Notes\Controllers'], function ()
{
    Route::post('create', ['uses' => 'NoteController@create', 'as' => 'notes.create']);
    Route::post('delete', ['uses' => 'NoteController@delete', 'as' => 'notes.delete']);
});