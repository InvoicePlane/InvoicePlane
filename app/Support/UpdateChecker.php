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

class UpdateChecker
{
    protected $currentVersion;

    public function __construct()
    {
        $check_url = 'https://ids.invoiceplane.com/updatecheck?cv=' . config('ip.version');
        $this->currentVersion = file_get_contents($check_url);
    }

    /**
     * Check to see if there is a newer version available for download.
     *
     * @return boolean
     */
    public function updateAvailable()
    {
        if (str_replace('-', '', $this->currentVersion) > str_replace('-', '', config('ip.version'))) {
            return true;
        }

        return false;
    }

    /**
     * Getter for current version.
     *
     * @return string
     */
    public function getCurrentVersion()
    {
        return $this->currentVersion;
    }
}