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

namespace FI\Modules\RecurringInvoices\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\RecurringInvoices\Support\RecurringInvoiceCalculate;

class RecurringInvoiceRecalculateController extends Controller
{
    private $recurringInvoiceCalculate;

    public function __construct(RecurringInvoiceCalculate $recurringInvoiceCalculate)
    {
        $this->recurringInvoiceCalculate = $recurringInvoiceCalculate;
    }

    public function recalculate()
    {
        try {
            $this->recurringInvoiceCalculate->calculateAll();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

        return response()->json(['success' => true, 'message' => trans('fi.recalculation_complete')], 200);
    }
}