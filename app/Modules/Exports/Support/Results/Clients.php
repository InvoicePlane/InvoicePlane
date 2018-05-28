<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Exports\Support\Results;

use FI\Modules\Clients\Models\Client;

class Clients implements SourceInterface
{
    public function getResults($params = [])
    {
        $client = Client::orderBy('name');

        return $client->get()->toArray();
    }
}