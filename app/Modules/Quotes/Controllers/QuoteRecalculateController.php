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

namespace FI\Modules\Quotes\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Quotes\Support\QuoteCalculate;

class QuoteRecalculateController extends Controller
{
    private $quoteCalculate;

    public function __construct(QuoteCalculate $quoteCalculate)
    {
        $this->quoteCalculate = $quoteCalculate;
    }

    public function recalculate()
    {
        try {
            $this->quoteCalculate->calculateAll();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => trans('ip.recalculation_complete'),
        ], 200);
    }
}