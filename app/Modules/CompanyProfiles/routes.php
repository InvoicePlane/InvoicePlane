<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\CompanyProfiles\Controllers'], function ()
{
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