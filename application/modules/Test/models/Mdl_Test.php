<?php

/**
 * Class Mdl_Test
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018, InvoicePlane
 * @license     http://opensource.org/licenses/MIT     MIT License
 * @link        https://invoiceplane.com
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

    /** @var array */
    public $date_fields = [
        'created_at',
        'modified_at',
        'sent_at',
    ];

    public function check()
    {
        return $this->table;
    }
}
