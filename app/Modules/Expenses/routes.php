<?php

Route::group([
    'middleware' => ['web', 'auth.admin'],
    'prefix' => 'expenses',
    'namespace' => 'IP\Modules\Expenses\Controllers',
], function () {
    Route::get('/', ['uses' => 'ExpenseController@index', 'as' => 'expenses.index']);
    Route::get('create', ['uses' => 'ExpenseCreateController@create', 'as' => 'expenses.create']);
    Route::post('create', ['uses' => 'ExpenseCreateController@store', 'as' => 'expenses.store']);
    Route::get('{id}/edit', ['uses' => 'ExpenseEditController@edit', 'as' => 'expenses.edit']);
    Route::post('{id}/edit', ['uses' => 'ExpenseEditController@update', 'as' => 'expenses.update']);
    Route::get('{id}/delete', ['uses' => 'ExpenseController@delete', 'as' => 'expenses.delete']);

    Route::group(['prefix' => 'bill'], function () {
        Route::post('create', ['uses' => 'ExpenseBillController@create', 'as' => 'expenseBill.create']);
        Route::post('store', ['uses' => 'ExpenseBillController@store', 'as' => 'expenseBill.store']);
    });

    Route::get('lookup/category',
        ['uses' => 'ExpenseLookupController@lookupCategory', 'as' => 'expenses.lookupCategory']);
    Route::get('lookup/vendor', ['uses' => 'ExpenseLookupController@lookupVendor', 'as' => 'expenses.lookupVendor']);

    Route::post('bulk/delete', ['uses' => 'ExpenseController@bulkDelete', 'as' => 'expenses.bulk.delete']);
});
