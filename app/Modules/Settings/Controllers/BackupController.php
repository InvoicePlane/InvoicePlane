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

namespace IP\Modules\Settings\Controllers;

use Ifsnop\Mysqldump\Mysqldump;
use IP\Http\Controllers\Controller;

class BackupController extends Controller
{
    public function database()
    {
        $default = config('database.default');
        $host = config('database.connections.' . $default . '.host');
        $dbname = config('database.connections.' . $default . '.database');
        $username = config('database.connections.' . $default . '.username');
        $password = config('database.connections.' . $default . '.password');
        $filename = storage_path('InvoicePlane_' . date('Y-m-d_H-i-s') . '.sql');

        $dump = new Mysqldump('mysql:host=' . $host . ';dbname=' . $dbname, $username, $password);
        $dump->start($filename);

        return response()->download($filename)->deleteFileAfterSend(true);
    }
}
