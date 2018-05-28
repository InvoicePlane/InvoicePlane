<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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