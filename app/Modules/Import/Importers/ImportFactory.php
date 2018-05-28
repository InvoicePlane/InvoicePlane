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

namespace FI\Modules\Import\Importers;

class ImportFactory
{
    public static function create($importType)
    {
        switch ($importType)
        {
            case 'clients':
                return app()->make('FI\Modules\Import\Importers\ClientImporter');
            case 'quotes':
                return app()->make('FI\Modules\Import\Importers\QuoteImporter');
            case 'invoices':
                return app()->make('FI\Modules\Import\Importers\InvoiceImporter');
            case 'payments':
                return app()->make('FI\Modules\Import\Importers\PaymentImporter');
            case 'invoiceItems':
                return app()->make('FI\Modules\Import\Importers\InvoiceItemImporter');
            case 'quoteItems':
                return app()->make('FI\Modules\Import\Importers\QuoteItemImporter');
            case 'itemLookups':
                return app()->make('FI\Modules\Import\Importers\ItemLookupImporter');
            case 'expenses':
                return app('FI\Modules\Import\Importers\ExpenseImporter');
        }
    }
}