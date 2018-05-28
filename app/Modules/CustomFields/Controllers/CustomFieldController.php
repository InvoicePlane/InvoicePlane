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

namespace FI\Modules\CustomFields\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\CustomFields\Models\CustomField;
use FI\Modules\CustomFields\Support\CustomFields;
use FI\Modules\CustomFields\Requests\CustomFieldStoreRequest;
use FI\Modules\CustomFields\Requests\CustomFieldUpdateRequest;
use FI\Traits\ReturnUrl;

class CustomFieldController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        $customFields = CustomField::sortable(['tbl_name' => 'asc', 'field_label' => 'asc'])->paginate(config('fi.resultsPerPage'));

        return view('custom_fields.index')
            ->with('customFields', $customFields)
            ->with('tableNames', CustomFields::tableNames());
    }

    public function create()
    {
        return view('custom_fields.form')
            ->with('editMode', false)
            ->with('tableNames', CustomFields::tableNames())
            ->with('fieldTypes', CustomFields::fieldTypes());
    }

    public function store(CustomFieldStoreRequest $request)
    {
        $input = $request->all();

        $input['column_name'] = CustomField::getNextColumnName($input['tbl_name']);

        CustomField::create($input);

        CustomField::createCustomColumn($input['tbl_name'], $input['column_name'], $input['field_type']);

        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('fi.record_successfully_created'));
    }

    public function edit($id)
    {
        $customField = CustomField::find($id);

        return view('custom_fields.form')
            ->with('editMode', true)
            ->with('customField', $customField)
            ->with('tableNames', CustomFields::tableNames())
            ->with('fieldTypes', CustomFields::fieldTypes());
    }

    public function update(CustomFieldUpdateRequest $request, $id)
    {
        $customField = CustomField::find($id);

        $customField->fill($request->except('tbl_name'));

        $customField->save();

        return redirect($this->getReturnUrl())
            ->with('alertInfo', trans('fi.record_successfully_updated'));
    }

    public function delete($id)
    {
        $customField = CustomField::find($id);

        CustomField::deleteCustomColumn($customField->tbl_name, $customField->column_name);

        CustomField::destroy($id);

        return redirect()->route('customFields.index')
            ->with('alert', trans('fi.record_successfully_deleted'));
    }
}
