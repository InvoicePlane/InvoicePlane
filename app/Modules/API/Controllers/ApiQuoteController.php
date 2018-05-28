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

use FI\Modules\API\Requests\APIInvoiceItemRequest;
use FI\Modules\API\Requests\APIQuoteItemRequest;
use FI\Modules\API\Requests\APIQuoteStoreRequest;
use FI\Modules\Clients\Models\Client;
use FI\Modules\Quotes\Models\Quote;
use FI\Modules\Quotes\Models\QuoteItem;
use FI\Modules\Users\Models\User;

class ApiQuoteController extends ApiController
{
    public function lists()
    {
        $quotes = Quote::select('quotes.*')
            ->with(['items.amount', 'client', 'amount', 'currency'])
            ->status(request('status'))
            ->sortable(['quote_date' => 'desc', 'LENGTH(number)' => 'desc', 'number' => 'desc'])
            ->paginate(config('fi.resultsPerPage'));

        return response()->json($quotes);
    }

    public function show()
    {
        return response()->json(Quote::with(['items.amount', 'client', 'amount', 'currency'])->find(request('id')));
    }

    public function store(APIQuoteStoreRequest $request)
    {
        $input = $request->except('key', 'signature', 'timestamp', 'endpoint');

        $input['user_id'] = User::where('client_id', 0)->where('api_public_key', $request->input('key'))->first()->id;

        $input['client_id'] = Client::firstOrCreateByUniqueName(request('client_name'))->id;

        unset($input['client_name']);

        return response()->json(Quote::create($input));
    }

    public function addItem(APIQuoteItemRequest $request)
    {
        $input = $request->except('key', 'signature', 'timestamp', 'endpoint');

        QuoteItem::create($input);
    }

    public function delete()
    {
        $validator = $this->validator->make(['id' => request('id')], ['id' => 'required']);

        if ($validator->fails())
        {
            return response()->json($validator->errors()->all(), 400);
        }

        if (Quote::find(request('id')))
        {
            Quote::destroy(request('id'));

            return response(200);
        }

        return response()->json([trans('fi.record_not_found')], 400);
    }
}