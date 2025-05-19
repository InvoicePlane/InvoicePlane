<?php

if ( ! defined('BASEPATH')) {
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
class Admin_Controller extends User_Controller
{
    public function __construct()
    {
        parent::__construct('user_type', 1);
        $this->setCacheHeaders();
    }

    protected function filter_input(): void
    {
        $input = $this->input->post();

        array_walk(
            $input,
            function (&$value, $key): void {
                if ( ! is_array($value)) {
                    $value = $this->security->xss_clean($value);
                    $value = strip_tags($value);
                    $value = html_escape($value);   // <<<=== that's a CodeIgniter helper
                }
            }
        );
    }

    protected function setCacheHeaders()
    {
        $this->output
            ->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0')
            ->set_header('Pragma: no-cache')
            ->set_header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

        $xFrameOptions = env('X_FRAME_OPTIONS');
        if ( ! empty($xFrameOptions)) {
            $this->output->set_header('X-Frame-Options: ' . $xFrameOptions);
        }

        if (env_bool('ENABLE_X_CONTENT_TYPE_OPTIONS', true) !== false) {
            $this->output->set_header('X-Content-Type-Options: nosniff');
        }
    }
}
