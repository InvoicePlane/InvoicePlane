<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\API\Controllers;

use Illuminate\Routing\Controller;

class ApiController extends Controller
{
    protected $validator;

    public function __construct()
    {
        $this->validator = app('Illuminate\Validation\Factory');
    }
}