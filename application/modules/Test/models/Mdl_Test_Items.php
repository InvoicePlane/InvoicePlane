<?php

/**
 * Class Mdl_Test_Items
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018, InvoicePlane
 * @license     http://opensource.org/licenses/MIT     MIT License
 * @link        https://invoiceplane.com
 */
class Mdl_Test_Items extends IP_Model
{
    /** @var string */
    public $table = 'test_items';

    /** @var string */
    public $primary_key = 'id';

    public $fields = [
        'id',
        'test_id',
        'name',
    ];
}
