<?php

namespace IP\Widgets\Dashboard\QuoteSummary\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class WidgetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register the view path.
        view()->addLocation(app_path('Widgets/Dashboard/QuoteSummary/Views'));

        // Register the widget view composer.
        view()->composer('QuoteSummaryWidget', 'IP\Widgets\Dashboard\QuoteSummary\Composers\QuoteSummaryWidgetComposer');

        // Register the setting view composer.
        view()->composer('QuoteSummaryWidgetSettings', 'IP\Widgets\Dashboard\QuoteSummary\Composers\QuoteSummarySettingComposer');

        // Widgets don't have route files so we'll place this here.
        Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'IP\Widgets\Dashboard\QuoteSummary\Controllers'], function () {
            Route::post('widgets/dashboard/quote_summary/render_partial', ['uses' => 'WidgetController@renderPartial', 'as' => 'widgets.dashboard.quoteSummary.renderPartial']);
        });
    }

    public function register()
    {
        //
    }
}