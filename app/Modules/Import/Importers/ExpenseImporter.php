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

namespace FI\Modules\Import\Importers;

use FI\Modules\Expenses\Models\Expense;
use Illuminate\Support\Facades\Validator;

class ExpenseImporter extends AbstractImporter
{
    public function getFields()
    {
        $fields = [
            'expense_date' => '* ' . trans('fi.date'),
            'category_name' => '* ' . trans('fi.category'),
            'amount' => '* ' . trans('fi.amount'),
            'vendor_name' => trans('fi.vendor'),
            'client_name' => trans('fi.client'),
            'description' => trans('fi.description'),
            'tax' => trans('fi.tax'),
            'company_profile' => trans('fi.company_profile'),
        ];

        return $fields;
    }

    public function getMapRules()
    {
        return [
            'expense_date' => 'required',
            'category_name' => 'required',
            'amount' => 'required',
        ];
    }

    public function getValidator($input)
    {
        return Validator::make($input, [
            'expense_date' => 'required',
            'category_name' => 'required',
            'amount' => 'required|numeric',
        ])->setAttributeNames([
            'user_id' => trans('fi.user'),
            'company_profile_id' => trans('fi.company_profile'),
            'expense_date' => trans('fi.date'),
            'category_name' => trans('fi.category'),
            'description' => trans('fi.description'),
            'amount' => trans('fi.amount'),
        ]);
    }

    public function importData($input)
    {
        $row = 1;

        $fields = [];

        foreach ($input as $key => $field) {
            if (is_numeric($field)) {
                $fields[$key] = $field;
            }
        }

        $handle = fopen(storage_path('expenses.csv'), 'r');

        if (!$handle) {
            $this->messages->add('error', 'Could not open the file');

            return false;
        }

        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            if ($row !== 1) {
                $record = [];

                foreach ($fields as $field => $key) {
                    $record[$field] = $data[$key];
                }

                if ($this->validateRecord($record)) {
                    Expense::create($record);
                }
            }

            $row++;
        }

        fclose($handle);

        return true;
    }
}