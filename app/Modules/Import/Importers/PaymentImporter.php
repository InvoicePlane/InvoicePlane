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

use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\PaymentMethods\Models\PaymentMethod;
use FI\Modules\Payments\Models\Payment;
use Illuminate\Support\Facades\Validator;

class PaymentImporter extends AbstractImporter
{
    public function getFields()
    {
        return [
            'paid_at'           => '* ' . trans('fi.date'),
            'invoice_id'        => '* ' . trans('fi.invoice_number'),
            'amount'            => '* ' . trans('fi.amount'),
            'payment_method_id' => '* ' . trans('fi.payment_method'),
            'note'              => trans('fi.note'),
        ];
    }

    public function getMapRules()
    {
        return [
            'paid_at'           => 'required',
            'invoice_id'        => 'required',
            'amount'            => 'required',
            'payment_method_id' => 'required',
        ];
    }

    public function getValidator($input)
    {
        return Validator::make($input, [
            'paid_at'           => 'required',
            'invoice_id'        => 'required',
            'amount'            => 'required|numeric',
            'payment_method_id' => 'required',
        ]);
    }

    public function importData($input)
    {
        $row = 1;

        $fields = [];

        foreach ($input as $field => $key)
        {
            if (is_numeric($key))
            {
                $fields[$key] = $field;
            }
        }

        $handle = fopen(storage_path('payments.csv'), 'r');

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

                foreach ($fields as $key => $field)
                {
                    $record[$field] = $data[$key];
                }

                // Attempt to format the date, otherwise use today
                if (strtotime($record['paid_at']))
                {
                    $record['paid_at'] = date('Y-m-d', strtotime($record['paid_at']));
                }
                else
                {
                    $record['paid_at'] = date('Y-m-d');
                }

                // Transform the invoice number to the id
                $record['invoice_id'] = Invoice::where('number', $record['invoice_id'])->first()->id;

                // Transform the payment method to the id
                if ($record['payment_method_id'] <> 'NULL')
                {
                    $record['payment_method_id'] = PaymentMethod::firstOrCreate(['name' => $record['payment_method_id']])->id;
                }
                else
                {
                    $record['payment_method_id'] = PaymentMethod::firstOrCreate(['name' => 'Other'])->id;
                }

                if (!isset($record['note']))
                {
                    $record['note'] = '';
                }

                if ($this->validateRecord($record))
                {
                    Payment::create($record);
                }
            }
            $row++;
        }

        fclose($handle);

        return true;
    }

    private $paymentValidator;
}