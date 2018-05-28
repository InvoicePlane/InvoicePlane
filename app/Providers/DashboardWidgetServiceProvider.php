<?php

namespace FI\Providers;

use FI\Support\Directory;
use Illuminate\Support\ServiceProvider;

class DashboardWidgetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $widgets = Directory::listContents(app_path('Widgets/Dashboard'));

        foreach ($widgets as $widget)
        {
            $providerPath          = app_path('Widgets/Dashboard/' . $widget . '/Providers/WidgetServiceProvider.php');
            $settingValidationPath = app_path('Widgets/Dashboard/' . $widget . '/SettingValidation.php');

            // Load the widget service provider if it exists.
            if (file_exists($providerPath))
            {
                app()->register('FI\Widgets\Dashboard\\' . $widget . '\Providers\WidgetServiceProvider');
            }

            // Register the widget setting validation rules if they exist.
            if (file_exists($settingValidationPath))
            {
                config(['fi.settingValidationRules.' . $widget => require($settingValidationPath)]);
            }
        }
    }

    public function register()
    {
        //
    }
}
