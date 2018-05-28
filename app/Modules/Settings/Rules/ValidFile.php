<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Settings\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidFile implements Rule
{
    public function passes($attribute, $value)
    {
        return is_file($value);
    }

    public function message()
    {
        return trans('fi.pdf_driver_wkhtmltopdf');
    }
}