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

use FI\Modules\Quotes\Models\Quote;

class Quotes implements SourceInterface
{
    public function getResults($params = [])
    {
        $quote = Quote::select('quotes.number', 'quotes.created_at', 'quotes.updated_at', 'quotes.expires_at',
            'quotes.terms', 'quotes.footer', 'quotes.url_key', 'quotes.currency_code', 'quotes.exchange_rate',
            'quotes.template', 'quotes.summary', 'groups.name AS group', 'clients.name AS client_name',
            'clients.email AS client_email', 'clients.address AS client_address', 'clients.city AS client_city',
            'clients.state AS client_state', 'clients.zip AS client_zip', 'clients.country AS client_country',
            'users.name AS user_name', 'users.email AS user_email',
            'company_profiles.company AS company', 'company_profiles.address AS company_address',
            'company_profiles.city AS company_city', 'company_profiles.state AS company_state',
            'company_profiles.zip AS company_zip', 'company_profiles.country AS company_country',
            'quote_amounts.subtotal', 'quote_amounts.tax', 'quote_amounts.total')
            ->join('quote_amounts', 'quote_amounts.quote_id', '=', 'quotes.id')
            ->join('clients', 'clients.id', '=', 'quotes.client_id')
            ->join('groups', 'groups.id', '=', 'quotes.group_id')
            ->join('users', 'users.id', '=', 'quotes.user_id')
            ->join('company_profiles', 'company_profiles.id', '=', 'quotes.company_profile_id')
            ->orderBy('number');

        return $quote->get()->toArray();
    }
}