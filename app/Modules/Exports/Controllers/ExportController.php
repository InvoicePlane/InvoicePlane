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