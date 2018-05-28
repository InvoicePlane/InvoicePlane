<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Users\Requests;

class UpdatePasswordRequest extends UserStoreRequest
{
    public function rules()
    {
        return [
            'password' => 'required|confirmed|min:6',
        ];
    }
}