<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Support\PDF;

use FI\Support\Directory;

class PDFFactory
{
    public static function create()
    {
        $class = 'FI\Support\PDF\Drivers\\' . config('fi.pdfDriver');

        return new $class;
    }

    public static function getDrivers()
    {
        $driverFiles = Directory::listContents(app_path('Support/PDF/Drivers'));
        $drivers     = [];

        foreach ($driverFiles as $driverFile)
        {
            $driver = str_replace('.php', '', $driverFile);

            $drivers[$driver] = $driver;
        }

        return $drivers;
    }
}