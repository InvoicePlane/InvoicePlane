<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\API\Requests;

use FI\Modules\Clients\Requests\ClientUpdateRequest;

class APIClientUpdateRequest extends ClientUpdateRequest
{
    public function rules()
    {
        return [
            'id'          => 'required',
            'email'       => 'email',
            'unique_name' => 'sometimes|unique:clients,unique_name,' . $this->input('id'),
        ];
    }
}