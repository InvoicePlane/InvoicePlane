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

namespace IP\Support\Calculators;

abstract class Calculator
{
    /**
     * The id of the quote or invoice.
     *
     * @var int
     */
    protected $id;

    /**
     * An array to store items.
     *
     * @var array
     */
    protected $items = [];

    /**
     * An array to store calculated item amounts.
     *
     * @var array
     */
    protected $calculatedItemAmounts = [];

    /**
     * An array to store overall calculated amounts.
     *
     * @var array
     */
    protected $calculatedAmount = [];

    /**
     * Whether or not the document is canceled.
     *
     * @var boolean
     */
    protected $isCanceled = false;

    protected $discount;

    /**
     * Initialize the calculated amount array.
     */
    public function __construct()
    {
        $this->calculatedAmount = [
            'subtotal' => 0,
            'discount' => 0,
            'tax' => 0,
            'total' => 0,
        ];
    }

    /**
     * Sets the id.
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    public function setIsCanceled($isCanceled)
    {
        $this->isCanceled = $isCanceled;
    }

    /**
     * Adds a item for calculation.
     *
     * @param int   $itemId
     * @param float $quantity
     * @param float $price
     * @param float $taxRatePercent
     * @param float $taxRate2Percent
     * @param int   $taxRate2IsCompound
     * @param int   $calculateVat
     */
    public function addItem($itemId, $quantity, $price, $taxRatePercent = 0.00, $taxRate2Percent = 0.00, $taxRate2IsCompound = 0, $calculateVat = 0)
    {
        $this->items[] = [
            'itemId' => $itemId,
            'quantity' => $quantity,
            'price' => $price,
            'taxRatePercent' => $taxRatePercent,
            'taxRate2Percent' => $taxRate2Percent,
            'taxRate2IsCompound' => $taxRate2IsCompound,
            'calculateVat' => $calculateVat,
        ];
    }

    /**
     * Call the calculation methods.
     */
    public function calculate()
    {
        $this->calculateItems();
    }

    /**
     * Calculates the items.
     */
    protected function calculateItems()
    {
        foreach ($this->items as $item) {
            $subtotal = round($item['quantity'] * $item['price'], 2);

            $discount = $subtotal * ($this->discount / 100);
            $discountedSubtotal = $subtotal - $discount;

            if ($item['taxRatePercent']) {
                if (!$item['calculateVat']) {
                    $tax1 = round($discountedSubtotal * ($item['taxRatePercent'] / 100), config('ip.roundTaxDecimals'));
                } else {
                    $tax1 = $discountedSubtotal - ($discountedSubtotal / (1 + $item['taxRatePercent'] / 100));
                    $subtotal = $subtotal - $tax1;
                }
            } else {
                $tax1 = 0;
            }

            if ($item['taxRate2Percent']) {
                if ($item['taxRate2IsCompound']) {
                    $tax2 = round(($discountedSubtotal + $tax1) * ($item['taxRate2Percent'] / 100), config('ip.roundTaxDecimals'));
                } else {
                    $tax2 = round($discountedSubtotal * ($item['taxRate2Percent'] / 100), config('ip.roundTaxDecimals'));
                }
            } else {
                $tax2 = 0;
            }

            $taxTotal = $tax1 + $tax2;
            $total = $subtotal + $taxTotal;

            $this->calculatedItemAmounts[] = [
                'item_id' => $item['itemId'],
                'subtotal' => $subtotal,
                'tax_1' => $tax1,
                'tax_2' => $tax2,
                'tax' => $taxTotal,
                'total' => $total,
            ];

            $this->calculatedAmount['subtotal'] += $subtotal;
            $this->calculatedAmount['discount'] += $discount;
            $this->calculatedAmount['tax'] += $taxTotal;
            $this->calculatedAmount['total'] += ($total - $discount);
        }
    }

    /**
     * Returns calculated item amounts.
     *
     * @return array
     */
    public function getCalculatedItemAmounts()
    {
        return $this->calculatedItemAmounts;
    }

    /**
     * Returns overall calculated amount.
     *
     * @return array
     */
    public function getCalculatedAmount()
    {
        return $this->calculatedAmount;
    }
}