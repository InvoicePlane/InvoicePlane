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

namespace IP\Support\PDF;

use IP\Support\Directory;

class PDFFactory
{
    public static function create()
    {
        $class = 'IP\Support\PDF\Drivers\\' . config('fi.pdfDriver');

        return new $class;
    }

    public static function getDrivers()
    {
        $driverFiles = Directory::listContents(app_path('Support/PDF/Drivers'));
        $drivers = [];

        foreach ($driverFiles as $driverFile) {
            $driver = str_replace('.php', '', $driverFile);

            $drivers[$driver] = $driver;
        }

        return $drivers;
    }
}