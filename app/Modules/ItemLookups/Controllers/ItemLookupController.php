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

namespace IP\Modules\ItemLookups\Controllers;

use IP\Http\Controllers\Controller;
use IP\Modules\ItemLookups\Models\ItemLookup;
use IP\Modules\ItemLookups\Requests\ItemLookupRequest;
use IP\Modules\TaxRates\Models\TaxRate;
use IP\Support\NumberFormatter;

class ItemLookupController extends Controller
{
    public function index()
    {
        $itemLookups = ItemLookup::sortable(['name' => 'asc'])
            ->with(['taxRate', 'taxRate2',])
            ->keywords(request('search'))
            ->paginate(config('ip.resultsPerPage'));

        return view('item_lookups.index')
            ->with('itemLookups', $itemLookups)
            ->with('displaySearch', true);
    }

    public function create()
    {
        return view('item_lookups.form')
            ->with('editMode', false)
            ->with('taxRates', TaxRate::getList());
    }

    public function store(ItemLookupRequest $request)
    {
        ItemLookup::create($request->all());

        return redirect()->route('itemLookups.index')
            ->with('alertSuccess', trans('ip.record_successfully_created'));
    }

    public function edit($id)
    {
        $itemLookup = ItemLookup::find($id);

        return view('item_lookups.form')
            ->with('editMode', true)
            ->with('itemLookup', $itemLookup)
            ->with('taxRates', TaxRate::getList());
    }

    public function update(ItemLookupRequest $request, $id)
    {
        $itemLookup = ItemLookup::find($id);

        $itemLookup->fill($request->all());

        $itemLookup->save();

        return redirect()->route('itemLookups.index')
            ->with('alertInfo', trans('ip.record_successfully_updated'));
    }

    public function delete($id)
    {
        ItemLookup::destroy($id);

        return redirect()->route('itemLookups.index')
            ->with('alert', trans('ip.record_successfully_deleted'));
    }

    public function ajaxItemLookup()
    {
        $items = ItemLookup::orderBy('name')->where('name', 'like', '%' . request('query') . '%')->get();

        $list = [];

        foreach ($items as $item) {
            $list[] = [
                'name' => $item->name,
                'description' => $item->description,
                'price' => NumberFormatter::format($item->price),
                'tax_rate_id' => $item->tax_rate_id,
                'tax_rate_2_id' => $item->tax_rate_2_id,
            ];
        }

        return json_encode($list);
    }
}
