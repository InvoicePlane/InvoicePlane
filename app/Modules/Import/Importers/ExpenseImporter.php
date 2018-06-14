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

namespace IP\Modules\Import\Importers;

use IP\Modules\Expenses\Models\Expense;
use Illuminate\Support\Facades\Validator;

class ExpenseImporter extends AbstractImporter
{
    public function getFields()
    {
        $fields = [
            'expense_date' => '* ' . trans('ip.date'),
            'category_name' => '* ' . trans('ip.category'),
            'amount' => '* ' . trans('ip.amount'),
            'vendor_name' => trans('ip.vendor'),
            'client_name' => trans('ip.client'),
            'description' => trans('ip.description'),
            'tax' => trans('ip.tax'),
            'company_profile' => trans('ip.company_profile'),
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
            'user_id' => trans('ip.user'),
            'company_profile_id' => trans('ip.company_profile'),
            'expense_date' => trans('ip.date'),
            'category_name' => trans('ip.category'),
            'description' => trans('ip.description'),
            'amount' => trans('ip.amount'),
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