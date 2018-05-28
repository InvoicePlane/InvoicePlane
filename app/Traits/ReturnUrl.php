<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Traits;

trait ReturnUrl
{
    public function setReturnUrl()
    {
        session(['returnUrl' => request()->fullUrl()]);
    }

    public function getReturnUrl()
    {
        return session('returnUrl');
    }
}