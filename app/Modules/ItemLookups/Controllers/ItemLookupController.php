<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\ItemLookups\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\ItemLookups\Models\ItemLookup;
use FI\Modules\ItemLookups\Requests\ItemLookupRequest;
use FI\Modules\TaxRates\Models\TaxRate;
use FI\Support\NumberFormatter;

class ItemLookupController extends Controller
{
    public function index()
    {
        $itemLookups = ItemLookup::sortable(['name' => 'asc'])->with(['taxRate', 'taxRate2'])->keywords(request('search'))->paginate(config('fi.resultsPerPage'));

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
            ->with('alertSuccess', trans('fi.record_successfully_created'));
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
            ->with('alertInfo', trans('fi.record_successfully_updated'));
    }

    public function delete($id)
    {
        ItemLookup::destroy($id);

        return redirect()->route('itemLookups.index')
            ->with('alert', trans('fi.record_successfully_deleted'));
    }

    public function ajaxItemLookup()
    {
        $items = ItemLookup::orderBy('name')->where('name', 'like', '%' . request('query') . '%')->get();

        $list = [];

        foreach ($items as $item)
        {
            $list[] = [
                'name'          => $item->name,
                'description'   => $item->description,
                'price'         => NumberFormatter::format($item->price),
                'tax_rate_id'   => $item->tax_rate_id,
                'tax_rate_2_id' => $item->tax_rate_2_id,
            ];
        }

        return json_encode($list);
    }
}