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

namespace FI\Modules\CustomFields\Support;

class CustomFields
{
    /**
     * Provide an array of available custom table names.
     *
     * @return array
     */
    public static function tableNames()
    {
        return [
            'clients' => trans('ip.clients'),
            'company_profiles' => trans('ip.company_profiles'),
            'expenses' => trans('ip.expenses'),
            'invoices' => trans('ip.invoices'),
            'quotes' => trans('ip.quotes'),
            'recurring_invoices' => trans('ip.recurring_invoices'),
            'payments' => trans('ip.payments'),
            'users' => trans('ip.users'),
        ];
    }

    /**
     * Provide an array of available custom field types.
     *
     * @return array
     */
    public static function fieldTypes()
    {
        return [
            'text' => trans('ip.text'),
            'dropdown' => trans('ip.dropdown'),
            'textarea' => trans('ip.textarea'),
        ];
    }
}