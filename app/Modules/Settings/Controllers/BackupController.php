<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Settings\Controllers;

use FI\Http\Controllers\Controller;
use Ifsnop\Mysqldump\Mysqldump;

class BackupController extends Controller
{
    public function database()
    {
        $default  = config('database.default');
        $host     = config('database.connections.' . $default . '.host');
        $dbname   = config('database.connections.' . $default . '.database');
        $username = config('database.connections.' . $default . '.username');
        $password = config('database.connections.' . $default . '.password');
        $filename = storage_path('InvoicePlane_' . date('Y-m-d_H-i-s') . '.sql');

        $dump = new Mysqldump('mysql:host=' . $host . ';dbname=' . $dbname, $username, $password);
        $dump->start($filename);

        return response()->download($filename)->deleteFileAfterSend(true);
    }
}