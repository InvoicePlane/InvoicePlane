<?php

Route::group(['prefix' => 'attachments', 'middleware' => 'web', 'namespace' => 'FI\Modules\Attachments\Controllers'], function () {
    Route::get('{urlKey}/download', ['uses' => 'AttachmentController@download', 'as' => 'attachments.download']);

    Route::group(['middleware' > 'auth.admin'], function () {
        Route::post('ajax/list', ['uses' => 'AttachmentController@ajaxList', 'as' => 'attachments.ajax.list']);
        Route::post('ajax/delete', ['uses' => 'AttachmentController@ajaxDelete', 'as' => 'attachments.ajax.delete']);
        Route::post('ajax/modal', ['uses' => 'AttachmentController@ajaxModal', 'as' => 'attachments.ajax.modal']);
        Route::post('ajax/upload', ['uses' => 'AttachmentController@ajaxUpload', 'as' => 'attachments.ajax.upload']);
        Route::post('ajax/access/update', ['uses' => 'AttachmentController@ajaxAccessUpdate', 'as' => 'attachments.ajax.access.update']);
    });
});