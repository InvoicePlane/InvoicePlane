<?php

namespace FI\Widgets\Dashboard\InvoiceSummary\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class WidgetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register the view path.
        view()->addLocation(app_path('Widgets/Dashboard/InvoiceSummary/Views'));

        // Register the widget view composer.
        view()->composer('InvoiceSummaryWidget', 'FI\Widgets\Dashboard\InvoiceSummary\Composers\InvoiceSummaryWidgetComposer');

        // Register the setting view composer.
        view()->composer('InvoiceSummaryWidgetSettings', 'FI\Widgets\Dashboard\InvoiceSummary\Composers\InvoiceSummarySettingComposer');

        // Widgets don't have route files so we'll place this here.
        Route::group(['middleware' => ['web','auth.admin'], 'namespace' => 'FI\Widgets\Dashboard\InvoiceSummary\Controllers'], function ()
        {
            Route::post('widgets/dashboard/invoice_summary/render_partial', ['uses' => 'WidgetController@renderPartial', 'as' => 'widgets.dashboard.invoiceSummary.renderPartial']);
        });
    }

    public function register()
    {
        //
    }
}