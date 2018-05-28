<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Clients\Requests;

class ClientUpdateRequest extends ClientStoreRequest
{
    public function rules()
    {
        $rules = parent::rules();

        $rules['unique_name'] = 'required|unique:clients,unique_name,' . $this->route('id');

        return $rules;
    }
}