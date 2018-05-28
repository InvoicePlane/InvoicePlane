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
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Groups\Models\Group;
use FI\Modules\Quotes\Models\Quote;
use Illuminate\Support\Facades\Validator;

class QuoteImporter extends AbstractImporter
{
    public function getFields()
    {
        return [
            'quote_date'      => '* ' . trans('fi.date'),
            'company_profile' => '* ' . trans('fi.company_profile'),
            'client_name'     => '* ' . trans('fi.client_name'),
            'number'          => '* ' . trans('fi.quote_number'),
            'group_id'        => trans('fi.group'),
            'expires_at'      => trans('fi.expires'),
            'summary'         => trans('fi.summary'),
            'terms'           => trans('fi.terms_and_conditions'),
        ];
    }

    public function getMapRules()
    {
        return [
            'quote_date'      => 'required',
            'company_profile' => 'required',
            'client_name'     => 'required',
            'number'          => 'required',
        ];
    }

    public function getValidator($input)
    {
        return Validator::make($input, [
                'client_id' => 'required|integer',
            ]
        );
    }

    public function importData($input)
    {
        $row             = 1;
        $fields          = [];
        $clients         = Client::select('id', 'unique_name')->get();
        $companyProfiles = CompanyProfile::get();
        $groups          = Group::get();
        $userId          = auth()->user()->id;

        foreach ($input as $field => $key)
        {
            if (is_numeric($key))
            {
                $fields[$key] = $field;
            }
        }

        $handle = fopen(storage_path('quotes.csv'), 'r');

        if (!$handle)
        {
            $this->messages->add('error', 'Could not open the file');

            return false;
        }

        while (($data = fgetcsv($handle, 1000, ',')) !== false)
        {
            if ($row !== 1)
            {
                $record = [];

                // Create the initial record from the file line
                foreach ($fields as $key => $field)
                {
                    $record[$field] = $data[$key];
                }

                // Replace the client name with the client id
                if ($client = $clients->where('unique_name', $record['client_name'])->first())
                {
                    $record['client_id'] = $client->id;
                }
                else
                {
                    $record['client_id'] = Client::create(['name' => $record['client_name']])->id;
                }

                unset($record['client_name']);

                // Replace the company profile name with the company profile id
                $companyProfile = $companyProfiles->where('name', $record['company_profile'])->first();

                if ($companyProfile)
                {
                    $record['company_profile_id'] = $companyProfile->id;
                }

                unset($record['company_profile']);

                // Format the created at date
                if (strtotime($record['quote_date']))
                {
                    $record['quote_date'] = date('Y-m-d', strtotime($record['quote_date']));
                }

                // Attempt to format this date if it exists.
                if (isset($record['expires_at']) and strtotime($record['expires_at']))
                {
                    $record['expires_at'] = date('Y-m-d', strtotime($record['expires_at']));
                }

                // Attempt to convert the group name to an id if it exists.
                if (isset($record['group_id']))
                {
                    $group = $groups->where('name', $record['group_id'])->first();

                    if ($group)
                    {
                        $record['group_id'] = $group->id;
                    }
                }

                // Assign the quote to the current logged in user
                $record['user_id'] = $userId;

                // The record *should* validate, but just in case...
                if ($this->validateRecord($record))
                {
                    Quote::create($record);
                }
            }
            $row++;
        }

        fclose($handle);

        return true;
    }
}