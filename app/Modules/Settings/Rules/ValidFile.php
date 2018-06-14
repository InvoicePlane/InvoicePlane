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

namespace IP\Modules\Settings\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidFile implements Rule
{
    public function passes($attribute, $value)
    {
        return is_file($value);
    }

    public function message()
    {
        return trans('ip.pdf_driver_wkhtmltopdf');
    }
}