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

namespace IP\Composers;

class SkinComposer
{
    public function compose($view)
    {
        $skin = (config('ip.skin') ?: 'skin-invoiceplane.min.css');
        $view->with('skin', $skin);
        $view->with('skinClass', str_replace('.min.css', '', $skin));
    }
}
