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

namespace IP\Modules\CustomFields\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentCustom extends Model
{
    /**
     * The table name
     * @var string
     */
    protected $table = 'payments_custom';

    /**
     * The primary key
     * @var string
     */
    protected $primaryKey = 'payment_id';

    /**
     * Guarded properties
     * @var array
     */
    protected $guarded = [];
}