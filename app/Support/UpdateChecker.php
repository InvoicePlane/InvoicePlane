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

class UpdateChecker
{
    protected $currentVersion;

    public function __construct()
    {
        $check_url = 'https://ids.invoiceplane.com/updatecheck?cv=' . config('fi.version');
        $this->currentVersion = file_get_contents($check_url);
    }

    /**
     * Check to see if there is a newer version available for download.
     *
     * @return boolean
     */
    public function updateAvailable()
    {
        if (str_replace('-', '', $this->currentVersion) > str_replace('-', '', config('fi.version'))) {
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