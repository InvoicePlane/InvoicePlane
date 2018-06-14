<?php

/**
 * InvoicePlane
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (C) 2014 - 2018 InvoicePlane
 * @license     https://invoiceplane.com/license
 * @link        https://invoiceplane.com
 *
 * Based on FusionInvoice by Jesse Terry (FusionInvoice, LLC)
 */

namespace IP\Modules\Quotes\Controllers;

use IP\Http\Controllers\Controller;
use IP\Modules\Clients\Models\Client;
use IP\Modules\CompanyProfiles\Models\CompanyProfile;
use IP\Modules\Groups\Models\Group;
use IP\Modules\Quotes\Models\Quote;
use IP\Modules\Quotes\Requests\QuoteStoreRequest;
use IP\Support\DateFormatter;

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

        $input['client_id'] = Client::firstOrCreateByUniqueName($request->input('client_name'))->id;
        $input['quote_date'] = DateFormatter::unformat($input['quote_date']);

        $quote = Quote::create($input);

        return response()->json(['id' => $quote->id], 200);
    }
}