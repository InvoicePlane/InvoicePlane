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
use FI\Modules\Quotes\Models\QuoteItem;
use FI\Modules\Quotes\Requests\QuoteStoreRequest;
use FI\Support\DateFormatter;

class QuoteCopyController extends Controller
{
    public function create()
    {
        $quote = Quote::find(request('quote_id'));

        return view('quotes._modal_copy')
            ->with('quote', $quote)
            ->with('groups', Group::getList())
            ->with('companyProfiles', CompanyProfile::getList())
            ->with('quote_date', DateFormatter::format())
            ->with('user_id', auth()->user()->id);
    }

    public function store(QuoteStoreRequest $request)
    {
        $client = Client::firstOrCreateByUniqueName($request->input('client_name'));

        $fromQuote = Quote::find($request->input('quote_id'));

        $toQuote = Quote::create([
            'client_id'          => $client->id,
            'company_profile_id' => $request->input('company_profile_id'),
            'quote_date'         => DateFormatter::unformat($request->input('quote_date')),
            'group_id'           => $request->input('group_id'),
            'currency_code'      => $fromQuote->currency_code,
            'exchange_rate'      => $fromQuote->exchange_rate,
            'terms'              => $fromQuote->terms,
            'footer'             => $fromQuote->footer,
            'template'           => $fromQuote->template,
            'summary'            => $fromQuote->summary,
            'discount'           => $fromQuote->discount,
        ]);

        foreach ($fromQuote->items as $item)
        {
            QuoteItem::create([
                'quote_id'      => $toQuote->id,
                'name'          => $item->name,
                'description'   => $item->description,
                'quantity'      => $item->quantity,
                'price'         => $item->price,
                'tax_rate_id'   => $item->tax_rate_id,
                'tax_rate_2_id' => $item->tax_rate_2_id,
                'display_order' => $item->display_order,
            ]);
        }

        // Copy the custom fields
        $custom = collect($fromQuote->custom)->except('quote_id')->toArray();
        $toQuote->custom->update($custom);

        return response()->json(['id' => $toQuote->id], 200);
    }
}