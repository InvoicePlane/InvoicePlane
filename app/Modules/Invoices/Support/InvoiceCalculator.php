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

namespace IP\Modules\Invoices\Support;

use IP\Support\Calculators\Calculator;
use IP\Support\Calculators\Interfaces\PayableInterface;

class InvoiceCalculator extends Calculator implements PayableInterface
{
    /**
     * Call the calculation methods.
     */
    public function calculate()
    {
        $this->calculateItems();
        $this->calculatePayments();
    }

    /**
     * Calculate additional properties.
     *
     * @return void
     */
    public function calculatePayments()
    {
        if (!$this->isCanceled) {
            $rounded_amount = round($this->calculatedAmount['total'], 2);
            $this->calculatedAmount['balance'] = $rounded_amount - $this->calculatedAmount['paid'];
        } else {
            $this->calculatedAmount['balance'] = 0;
        }
    }

    /**
     * Set the total paid amount.
     *
     * @param float $totalPaid
     */
    public function setTotalPaid($totalPaid)
    {
        if ($totalPaid) {
            $this->calculatedAmount['paid'] = $totalPaid;
        } else {
            $this->calculatedAmount['paid'] = 0;
        }
    }
}
