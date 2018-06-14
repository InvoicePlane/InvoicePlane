<?php

namespace IP\Modules\Merchant\Support;

use IP\Support\Directory;

class MerchantFactory
{
    public static function getDrivers($enabledOnly = false)
    {
        $files = Directory::listContents(app_path('Modules/Merchant/Support/Drivers'));

        $drivers = [];

        foreach ($files as $file) {
            $file = basename($file, '.php');

            $driver = self::create($file);

            if (!$enabledOnly or $enabledOnly and $driver->getSetting('enabled')) {
                $drivers[$file] = $driver;
            }
        }

        return $drivers;
    }

    /**
     * @return MerchantDriver
     */
    public static function create($driver)
    {
        $driver = 'IP\\Modules\\Merchant\\Support\\Drivers\\' . $driver;

        return new $driver;
    }
}