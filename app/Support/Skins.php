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

namespace IP\Support;

class Skins
{
    public static function lists()
    {
        $skins = Directory::listAssocContents(public_path('assets/dist/adminlte/css/skins'));

        unset($skins['_all-skins.css'], $skins['_all-skins.min.css']);

        foreach ($skins as $skin) {
            if (!strstr($skin, '.min.css')) {
                unset($skins[$skin]);
                continue;
            }

            $skins[$skin] = str_replace('skin-', '', $skins[$skin]);
            $skins[$skin] = str_replace('.min.css', '', $skins[$skin]);
        }

        return $skins;
    }
}
