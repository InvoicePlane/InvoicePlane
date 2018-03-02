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
    /** @var string */
    public $table = 'tests';

    /** @var string */
    public $primary_key = 'id';

    /** @var array */
    protected $joins = [
        'Mdl_Test_Items' => 'test_id'
    ];

    /** @var array */
    public $fields = [
        'id',
        'name',
    ];

    public function check()
    {
        return $this->table;
    }
}
