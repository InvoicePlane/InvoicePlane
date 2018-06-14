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

namespace IP\Modules\Import\Controllers;

use IP\Http\Controllers\Controller;
use IP\Modules\Import\Importers\ImportFactory;
use IP\Modules\Import\Requests\ImportRequest;

class ImportController extends Controller
{
    public function index()
    {
        $importTypes = [
            'clients' => trans('ip.clients'),
            'quotes' => trans('ip.quotes'),
            'quoteItems' => trans('ip.quote_items'),
            'invoices' => trans('ip.invoices'),
            'invoiceItems' => trans('ip.invoice_items'),
            'payments' => trans('ip.payments'),
            'expenses' => trans('ip.expenses'),
            'itemLookups' => trans('ip.item_lookups'),
        ];

        return view('import.index')
            ->with('importTypes', $importTypes);
    }

    public function upload(ImportRequest $request)
    {
        $request->file('import_file')->move(storage_path(), $request->input('import_type') . '.csv');

        return redirect()->route('import.map', [$request->input('import_type')]);
    }

    public function mapImport($importType)
    {
        $importer = ImportFactory::create($importType);

        return view('import.map')
            ->with('importType', $importType)
            ->with('importFields', $importer->getFields($importType))
            ->with('fileFields', $importer->getFileFields(storage_path($importType . '.csv')));
    }

    public function mapImportSubmit($importType)
    {
        $importer = ImportFactory::create($importType);

        if (!$importer->validateMap(request()->all())) {
            return redirect()->route('import.map', [$importType])
                ->withErrors($importer->errors())
                ->withInput();
        }

        if (!$importer->importData(request()->except('_token'))) {
            return redirect()->route('import.map', [$importType])
                ->withErrors($importer->errors());
        }

        return redirect()->route('import.index')
            ->with('alertInfo', trans('ip.records_imported_successfully'));
    }
}