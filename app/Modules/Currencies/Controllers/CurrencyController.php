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

namespace IP\Modules\Currencies\Controllers;

use IP\Http\Controllers\Controller;
use IP\Modules\Currencies\Models\Currency;
use IP\Modules\Currencies\Requests\CurrencyStoreRequest;
use IP\Modules\Currencies\Requests\CurrencyUpdateRequest;
use IP\Modules\Currencies\Support\CurrencyConverterFactory;
use IP\Traits\ReturnUrl;

class CurrencyController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        $currencies = Currency::sortable(['name' => 'asc'])->paginate(config('fi.resultsPerPage'));

        return view('currencies.index')
            ->with('currencies', $currencies)
            ->with('baseCurrency', config('fi.baseCurrency'));
    }

    public function create()
    {
        return view('currencies.form')
            ->with('editMode', false);
    }

    public function store(CurrencyStoreRequest $request)
    {
        Currency::create($request->all());

        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('ip.record_successfully_created'));
    }

    public function edit($id)
    {
        return view('currencies.form')
            ->with('editMode', true)
            ->with('currency', Currency::find($id));
    }

    public function update(CurrencyUpdateRequest $request, $id)
    {
        $currency = Currency::find($id);

        $currency->fill($request->all());

        $currency->save();

        return redirect($this->getReturnUrl())
            ->with('alertInfo', trans('ip.record_successfully_updated'));
    }

    public function delete($id)
    {
        $currency = Currency::find($id);

        if ($currency->in_use) {
            $alert = trans('ip.cannot_delete_record_in_use');
        } else {
            Currency::destroy($id);

            $alert = trans('ip.record_successfully_deleted');
        }

        return redirect()->route('currencies.index')
            ->with('alert', $alert);
    }

    public function getExchangeRate()
    {
        $currencyConverter = CurrencyConverterFactory::create();

        return $currencyConverter->convert(config('fi.baseCurrency'), request('currency_code'));
    }
}