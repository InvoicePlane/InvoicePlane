<?php

if (! defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

#[AllowDynamicProperties]
class Get extends Base_Controller
{
    public function attachment($filename)
    {
        $path = UPLOADS_CFILES_FOLDER;
        $filePath = realpath($path . $filename);

        if (strpos($filePath, $path) !== 0)
        {
            header("Status: 403 Forbidden");
            echo '<h1>Forbidden</h1>';
            exit;
        }

        if (! file_exists($filePath))
        {
            show_404();
        }

        header("Expires: -1");
        header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/octet-stream");
        header("Content-Length: " . filesize($filePath));

        echo file_get_contents($filePath);
        exit;
    }

}
