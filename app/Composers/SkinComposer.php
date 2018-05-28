<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Composers;

class SkinComposer
{
    public function compose($view)
    {
        $skin = (config('fi.skin') ?: 'skin-fusioninvoice.min.css');
        $view->with('skin', $skin);
        $view->with('skinClass', str_replace('.min.css', '', $skin));
    }
}