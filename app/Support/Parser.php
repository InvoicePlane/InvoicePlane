<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Support;

class Parser
{
    public function __construct($object)
    {
        $this->object = $object;

        $this->class = class_basename(get_class($object));
    }

    public function parse($template)
    {
        try
        {
            return view('app.email_templates.' . $template)
                ->with(strtolower($this->class), $this->object)
                ->render();
        }
        catch (\Exception $e)
        {
            abort(500);
        }
    }
}