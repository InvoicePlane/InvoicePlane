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

class QuoteCustom extends Model
{
    /**
     * The table name
     * @var string
     */
    protected $table = 'quotes_custom';

    /**
     * The primary key
     * @var string
     */
    protected $primaryKey = 'quote_id';

    /**
     * Guarded properties
     * @var array
     */
    protected $guarded = [];
}