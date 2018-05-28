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

namespace FI\Modules\Invoices\Support;

use FI\Support\Directory;

class InvoiceTemplates
{
    /**
     * Returns an array of invoice templates.
     *
     * @return array
     */
    public static function lists()
    {
        $defaultTemplates = Directory::listAssocContents(app_path('Modules/Templates/Views/templates/invoices'));

        $customTemplates = Directory::listAssocContents(base_path('custom/templates/invoice_templates'));

        return $defaultTemplates + $customTemplates;
    }
}