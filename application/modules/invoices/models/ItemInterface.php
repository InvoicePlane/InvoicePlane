<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Item interface ensuring proper interaction among classes.
 *
 * @author Michael Munger <mj@hph.io>
 */
interface ItemInterface
{
    public function get_values();
}
