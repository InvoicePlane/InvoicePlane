<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

/**
 * Sign validation for the amounts a client submits when saving an invoice or quote.
 *
 * standardize_amount() only normalises locale separators, so a submitted quantity, price or
 * discount of "-3" would otherwise be stored and summed as-is: (-3) x (-150) inflates the total
 * and (-10) x 50 turns the document into a negative bill (CWE-20 / CWE-840).
 *
 * Credit invoices are the one legitimate exception: they carry negative quantities by design
 * (copy_credit_invoice() negates them), so for a credit document the quantity must be zero or
 * negative and the price must still be non-negative.
 *
 * @param iterable|null $items            decoded "items" payload
 * @param mixed         $discount_amount  raw global discount amount input
 * @param mixed         $discount_percent raw global discount percent input
 * @param bool          $credit_document  true when the document is a credit invoice
 * @param string        $discount_prefix  "invoice" or "quote": prefix of the global discount field names
 *
 * @return array<string, string> translated error message per offending field (empty when valid)
 */
function amount_sign_errors($items, $discount_amount, $discount_percent, bool $credit_document = false, string $discount_prefix = 'invoice'): array
{
    $errors = [];

    if (is_iterable($items)) {
        foreach ($items as $item) {
            if (empty($item->item_name)) {
                continue;
            }

            $quantity = _submitted_amount($item->item_quantity ?? null);
            $price    = _submitted_amount($item->item_price ?? null);
            $discount = _submitted_amount($item->item_discount_amount ?? null);

            if ($credit_document ? $quantity > 0 : $quantity < 0) {
                $errors['item_quantity'] = trans($credit_document ? 'item_quantity_must_not_be_positive_on_credit' : 'item_quantity_must_not_be_negative');
            }

            if ($price < 0) {
                $errors['item_price'] = trans('item_price_must_not_be_negative');
            }

            if ($discount < 0) {
                $errors['item_discount_amount'] = trans('item_discount_must_not_be_negative');
            }
        }
    }

    if (_submitted_amount($discount_amount) < 0) {
        $errors[$discount_prefix . '_discount_amount'] = trans('discount_must_not_be_negative');
    }

    if (_submitted_amount($discount_percent) < 0) {
        $errors[$discount_prefix . '_discount_percent'] = trans('discount_must_not_be_negative');
    }

    if ($discount_prefix === 'quote' && _submitted_amount($discount_percent) > 100) {
        $errors['quote_discount_percent'] = trans('discount_percent_must_not_exceed_100');
    }

    return $errors;
}

/**
 * @param mixed $value raw submitted amount (string, number or empty)
 */
function _submitted_amount($value): float
{
    if ($value === null || $value === '') {
        return 0.0;
    }

    return (float) standardize_amount($value);
}
