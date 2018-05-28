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

namespace FI\Composers;

class SkinComposer
{
    public function compose($view)
    {
        $skin = (config('fi.skin') ?: 'skin-invoiceplane.min.css');
        $view->with('skin', $skin);
        $view->with('skinClass', str_replace('.min.css', '', $skin));
    }
}