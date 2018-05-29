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

namespace FI\Support\Statuses;

abstract class AbstractStatuses
{
    public static function statuses()
    {
        return static::$statuses;
    }

    /**
     * Returns an array of statuses to populate dropdown list.
     *
     * @return array
     */
    public static function lists()
    {
        $statuses = static::$statuses;

        unset($statuses[0]);

        foreach ($statuses as $key => $status) {
            $statuses[$key] = trans('fi.' . $status);
        }

        return $statuses;
    }

    public static function listsAllFlat()
    {
        $statuses = [];

        foreach (static::$statuses as $status) {
            $statuses[$status] = trans('fi.' . $status);
        }

        return $statuses;
    }

    /**
     * Returns the status key.
     *
     * @param  string $value
     * @return integer
     */
    public static function getStatusId($value)
    {
        return array_search($value, static::$statuses);
    }
}