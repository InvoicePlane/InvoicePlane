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

namespace FI\Modules\Groups\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Groups\GroupOptions;
use FI\Modules\Groups\Models\Group;
use FI\Modules\Groups\Requests\GroupRequest;
use FI\Traits\ReturnUrl;

class GroupController extends Controller
{
    use ReturnUrl;

    private $groupOptions;

    public function __construct(GroupOptions $groupOptions)
    {
        $this->groupOptions = $groupOptions;
    }

    public function index()
    {
        $this->setReturnUrl();

        $groups = Group::sortable(['name' => 'asc'])->paginate(config('fi.resultsPerPage'));

        return view('groups.index')
            ->with('groups', $groups)
            ->with('resetNumberOptions', $this->groupOptions->resetNumberOptions());
    }

    public function create()
    {
        return view('groups.form')
            ->with('editMode', false)
            ->with('resetNumberOptions', $this->groupOptions->resetNumberOptions());
    }

    public function store(GroupRequest $request)
    {
        Group::create($request->all());

        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('fi.record_successfully_created'));
    }

    public function edit($id)
    {
        $group = Group::find($id);

        return view('groups.form')
            ->with('editMode', true)
            ->with('group', $group)
            ->with('resetNumberOptions', $this->groupOptions->resetNumberOptions());
    }

    public function update(GroupRequest $request, $id)
    {
        $group = Group::find($id);

        $group->fill($request->all());

        $group->save();

        return redirect($this->getReturnUrl())
            ->with('alertInfo', trans('fi.record_successfully_updated'));
    }

    public function delete($id)
    {
        Group::destroy($id);

        return redirect()->route('groups.index')
            ->with('alert', trans('fi.record_successfully_deleted'));
    }
}