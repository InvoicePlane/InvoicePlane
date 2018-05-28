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

namespace FI\Support;

class DashboardWidgets
{
    public static function lists()
    {
        return Directory::listContents(__DIR__ . '/../Widgets/Dashboard');
    }

    public static function listsByOrder()
    {
        $widgets    = self::lists();
        $return     = [];
        $unassigned = 100;

        foreach ($widgets as $widget)
        {
            if (!$displayOrder = config('fi.widgetDisplayOrder' . $widget))
            {
                $displayOrder = $unassigned;
                $unassigned++;
            }

            $return[str_pad($displayOrder, 3, 0, STR_PAD_LEFT) . '-' . $widget] = $widget;
        }

        ksort($return);

        return $return;
    }
}