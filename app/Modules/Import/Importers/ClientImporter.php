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

use FI\Modules\Clients\Models\Client;
use FI\Modules\CustomFields\Models\CustomField;
use Illuminate\Support\Facades\Validator;

class ClientImporter extends AbstractImporter
{
    public function getFields()
    {
        $fields = [
            'name' => '* ' . trans('ip.name'),
            'unique_name' => trans('ip.unique_name'),
            'address' => trans('ip.address'),
            'city' => trans('ip.city'),
            'state' => trans('ip.state'),
            'zip' => trans('ip.postal_code'),
            'country' => trans('ip.country'),
            'phone' => trans('ip.phone'),
            'fax' => trans('ip.fax'),
            'mobile' => trans('ip.mobile'),
            'email' => trans('ip.email'),
            'web' => trans('ip.web'),
        ];

        foreach (CustomField::forTable('clients')->get() as $customField) {
            $fields['custom_' . $customField->column_name] = $customField->field_label;
        }

        return $fields;
    }

    public function getMapRules()
    {
        return ['name' => 'required'];
    }

    public function getValidator($input)
    {
        return Validator::make($input, [
            'name' => 'required',
            'email' => 'email',
        ])->setAttributeNames(['name' => trans('ip.name')]);
    }

    public function importData($input)
    {
        $row = 1;

        $fields = [];
        $customFields = [];

        foreach ($input as $key => $field) {
            if (is_numeric($field)) {
                if (substr($key, 0, 7) != 'custom_') {
                    $fields[$key] = $field;
                } else {
                    $customFields[substr($key, 7)] = $field;
                }
            }
        }

        $handle = fopen(storage_path('clients.csv'), 'r');

        if (!$handle) {
            $this->messages->add('error', 'Could not open the file');

            return false;
        }

        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            if ($row !== 1) {
                $record = [];

                $customRecord = [];

                foreach ($fields as $field => $key) {
                    $record[$field] = $data[$key];
                }

                if ($this->validateRecord($record)) {
                    $client = Client::create($record);

                    if ($customFields) {
                        foreach ($customFields as $field => $key) {
                            $customRecord[$field] = $data[$key];
                        }

                        $client->custom->update($customRecord);
                    }
                }
            }

            $row++;
        }

        fclose($handle);

        return true;
    }
}