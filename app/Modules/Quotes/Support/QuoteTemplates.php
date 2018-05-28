<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Quotes\Support;

use FI\Support\Directory;

class QuoteTemplates
{
    /**
     * Returns an array of quote templates.
     *
     * @return array
     */
    public static function lists()
    {
        $defaultTemplates = Directory::listAssocContents(app_path('Modules/Templates/Views/templates/quotes'));

        $customTemplates = Directory::listAssocContents(base_path('custom/templates/quote_templates'));

        return $defaultTemplates + $customTemplates;
    }
}