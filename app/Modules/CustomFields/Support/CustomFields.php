<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
            'clients'            => trans('fi.clients'),
            'company_profiles'   => trans('fi.company_profiles'),
            'expenses'           => trans('fi.expenses'),
            'invoices'           => trans('fi.invoices'),
            'quotes'             => trans('fi.quotes'),
            'recurring_invoices' => trans('fi.recurring_invoices'),
            'payments'           => trans('fi.payments'),
            'users'              => trans('fi.users'),
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
            'text'     => trans('fi.text'),
            'dropdown' => trans('fi.dropdown'),
            'textarea' => trans('fi.textarea'),
        ];
    }
}