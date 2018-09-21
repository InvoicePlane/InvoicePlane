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

namespace IP\Support\ProfileImage;

class ProfileImageFactory
{
    public static function create()
    {
        $class = 'IP\Support\ProfileImage\Drivers\\' . config('ip.profileImageDriver', 'Gravatar');

        return new $class;
    }

    public static function getDrivers()
    {
        $driverFiles = Directory::listContents(app_path('Support/ProfileImage/Drivers'));
        $drivers = [];

        foreach ($driverFiles as $driverFile) {
            $driver = str_replace('.php', '', $driverFile);

            $drivers[$driver] = $driver;
        }

        return $drivers;
    }
}