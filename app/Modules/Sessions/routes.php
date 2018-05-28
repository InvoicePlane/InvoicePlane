<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['namespace' => 'FI\Modules\Sessions\Controllers', 'middleware' => 'web'], function ()
{
    Route::get('login', ['uses' => 'SessionController@login', 'as' => 'session.login']);
    Route::post('login', ['uses' => 'SessionController@attempt', 'as' => 'session.attempt']);
    Route::get('logout', ['uses' => 'SessionController@logout', 'as' => 'session.logout']);
});