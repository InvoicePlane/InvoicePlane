<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Exports\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Exports\Support\Export;

class ExportController extends Controller
{
    public function index()
    {
        return view('export.index')
            ->with('writers', ['CsvWriter' => 'CSV', 'JsonWriter' => 'JSON', 'XlsWriter' => 'XLS', 'XmlWriter' => 'XML']);
    }

    public function export($exportType)
    {
        $export = new Export($exportType, request('writer'));

        $export->writeFile();

        return response()->download($export->getDownloadPath());
    }
}