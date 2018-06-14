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

class Languages
{
    /**
     * Provide a list of the available language translations.
     *
     * @return array
     */
    static function listLanguages()
    {
        $directories = Directory::listContents(base_path('resources/lang'));

        $languages = [];

        foreach ($directories as $directory) {
            $languages[$directory] = $directory;
        }

        return $languages;
    }
}