<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Quotes\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Clients\Models\Client;
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Groups\Models\Group;
use FI\Modules\Quotes\Models\Quote;
use FI\Modules\Quotes\Requests\QuoteStoreRequest;
use FI\Support\DateFormatter;

class QuoteCreateController extends Controller
{
    public function create()
    {
        return view('quotes._modal_create')
            ->with('companyProfiles', CompanyProfile::getList())
            ->with('groups', Group::getList());
    }

    public function store(QuoteStoreRequest $request)
    {
        $input = $request->except('client_name');

        $input['client_id']  = Client::firstOrCreateByUniqueName($request->input('client_name'))->id;
        $input['quote_date'] = DateFormatter::unformat($input['quote_date']);

        $quote = Quote::create($input);

        return response()->json(['id' => $quote->id], 200);
    }
}