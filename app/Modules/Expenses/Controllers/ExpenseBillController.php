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

namespace IP\Modules\Expenses\Controllers;

use IP\Http\Controllers\Controller;
use IP\Modules\Expenses\Models\Expense;
use IP\Modules\Expenses\Requests\ExpenseBillRequest;
use IP\Modules\Invoices\Models\InvoiceItem;

class ExpenseBillController extends Controller
{
    public function create()
    {
        $expense = Expense::defaultQuery()->find(request('id'));

        $clientInvoices = $expense->client->invoices()->orderBy('created_at', 'desc')->statusIn([
            'draft',
            'sent',
        ])->get();

        $invoices = [];

        foreach ($clientInvoices as $invoice) {
            $invoices[$invoice->id] = $invoice->formatted_created_at . ' - ' . $invoice->number . ' ' . $invoice->summary;
        }

        return view('expenses._modal_bill')
            ->with('expense', $expense)
            ->with('invoices', $invoices)
            ->with('redirectTo', request('redirectTo'));
    }

    public function store(ExpenseBillRequest $request)
    {
        $expense = Expense::find(request('id'));

        $expense->invoice_id = request('invoice_id');

        $expense->save();

        if (request('add_line_item')) {
            $item = [
                'invoice_id' => request('invoice_id'),
                'name' => request('item_name'),
                'description' => request('item_description'),
                'quantity' => 1,
                'price' => $expense->amount,
            ];

            InvoiceItem::create($item);
        }
    }
}