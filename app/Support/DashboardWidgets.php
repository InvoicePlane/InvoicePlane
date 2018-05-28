<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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