<?php

Route::group(['prefix' => 'merchant', 'middleware' => 'web', 'namespace' => 'FI\Modules\Merchant\Controllers'], function ()
{
    Route::post('pay', ['uses' => 'MerchantController@pay', 'as' => 'merchant.pay']);
    Route::get('{driver}/{urlKey}/cancel', ['uses' => 'MerchantController@cancelUrl', 'as' => 'merchant.cancelUrl']);
    Route::get('{driver}/{urlKey}/return', ['uses' => 'MerchantController@returnUrl', 'as' => 'merchant.returnUrl']);
    Route::post('{driver}/{urlKey}/webhook', ['uses' => 'MerchantController@webhookUrl', 'as' => 'merchant.webhookUrl']);
});