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

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\CompanyProfiles\Controllers'], function () {
    Route::get('company_profiles', ['uses' => 'CompanyProfileController@index', 'as' => 'companyProfiles.index']);
    Route::get('company_profiles/create', ['uses' => 'CompanyProfileController@create', 'as' => 'companyProfiles.create']);
    Route::get('company_profiles/{id}/edit', ['uses' => 'CompanyProfileController@edit', 'as' => 'companyProfiles.edit']);
    Route::get('company_profiles/{id}/delete', ['uses' => 'CompanyProfileController@delete', 'as' => 'companyProfiles.delete']);

    Route::post('company_profiles', ['uses' => 'CompanyProfileController@store', 'as' => 'companyProfiles.store']);
    Route::post('company_profiles/{id}', ['uses' => 'CompanyProfileController@update', 'as' => 'companyProfiles.update']);

    Route::post('company_profiles/{id}/delete_logo', ['uses' => 'CompanyProfileController@deleteLogo', 'as' => 'companyProfiles.deleteLogo']);
    Route::post('company_profiles/ajax/modal_lookup', ['uses' => 'CompanyProfileController@ajaxModalLookup', 'as' => 'companyProfiles.ajax.modalLookup']);
});

Route::get('company_profiles/{id}/logo', ['uses' => 'FI\Modules\CompanyProfiles\Controllers\LogoController@logo', 'as' => 'companyProfiles.logo']);