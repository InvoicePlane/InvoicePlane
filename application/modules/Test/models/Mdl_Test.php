<?php

/**
 * Class Mdl_Test
 *
 * @author         InvoicePlane Developers & Contributors
 * @copyright      Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license        https://invoiceplane.com/license.txt
 * @link           https://invoiceplane.com
 */
class Mdl_Test extends IP_Model
{
    public $table = 'tests';

    public function check()
    {
        return $this->table;
    }
}
