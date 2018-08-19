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

namespace IP\Modules\Invoices\Controllers;

use IP\Http\Controllers\Controller;
use IP\Modules\Currencies\Models\Currency;
use IP\Modules\CustomFields\Models\CustomField;
use IP\Modules\Invoices\Models\Invoice;
use IP\Modules\Invoices\Models\InvoiceItem;
use IP\Modules\Invoices\Requests\InvoiceUpdateRequest;
use IP\Modules\Invoices\Support\InvoiceTemplates;
use IP\Modules\ItemLookups\Models\ItemLookup;
use IP\Modules\TaxRates\Models\TaxRate;
use IP\Support\DateFormatter;
use IP\Support\Statuses\InvoiceStatuses;
use IP\Traits\ReturnUrl;

class InvoiceEditController extends Controller
{
    use ReturnUrl;

    public function edit($id)
    {
        $invoice = Invoice::with(['items.amount.item.invoice.currency'])->find($id);
        $invoice_item_count = isset($invoice->invoiceItems) ? count($invoice->invoiceItems) : 0;

        return view('invoices.edit')
            ->with('invoice', $invoice)
            ->with('statuses', InvoiceStatuses::lists())
            ->with('currencies', Currency::getList())
            ->with('taxRates', TaxRate::getList())
            ->with('customFields', CustomField::forTable('invoices')->get())
            ->with('returnUrl', $this->getReturnUrl())
            ->with('templates', InvoiceTemplates::lists())
            ->with('itemCount', $invoice_item_count);
    }

    public function update(InvoiceUpdateRequest $request, $id)
    {
        // Unformat the invoice dates.
        $invoiceInput = $request->except(['items', 'custom', 'apply_exchange_rate']);
        $invoiceInput['invoice_date'] = DateFormatter::unformat($invoiceInput['invoice_date']);
        $invoiceInput['due_at'] = DateFormatter::unformat($invoiceInput['due_at']);

        // Save the invoice.
        $invoice = Invoice::find($id);
        $invoice->fill($invoiceInput);
        $invoice->save();

        // Save the items.
        foreach ($request->input('items') as $item) {
            $item['apply_exchange_rate'] = request('apply_exchange_rate');

            if (!isset($item['id']) or (!$item['id'])) {
                $saveItemAsLookup = $item['save_item_as_lookup'];
                unset($item['save_item_as_lookup']);

                InvoiceItem::create($item);

                if ($saveItemAsLookup) {
                    ItemLookup::create([
                        'name' => $item['name'],
                        'description' => $item['description'],
                        'price' => $item['price'],
                        'tax_rate_id' => $item['tax_rate_id'],
                        'tax_rate_2_id' => $item['tax_rate_2_id'],
                    ]);
                }
            } else {
                $invoiceItem = InvoiceItem::find($item['id']);
                $invoiceItem->fill($item);
                $invoiceItem->save();
            }
        }
    }

    public function refreshEdit($id)
    {
        $invoice = Invoice::with(['items.amount.item.invoice.currency'])->find($id);

        return view('invoices._edit')
            ->with('invoice', $invoice)
            ->with('statuses', InvoiceStatuses::lists())
            ->with('currencies', Currency::getList())
            ->with('taxRates', TaxRate::getList())
            ->with('customFields', CustomField::forTable('invoices')->get())
            ->with('returnUrl', $this->getReturnUrl())
            ->with('templates', InvoiceTemplates::lists())
            ->with('itemCount', count($invoice->invoiceItems));
    }

    public function refreshTotals()
    {
        return view('invoices._edit_totals')
            ->with('invoice', Invoice::with(['items.amount.item.invoice.currency'])->find(request('id')));
    }

    public function refreshTo()
    {
        return view('invoices._edit_to')
            ->with('invoice', Invoice::find(request('id')));
    }

    public function refreshFrom()
    {
        return view('invoices._edit_from')
            ->with('invoice', Invoice::find(request('id')));
    }

    public function updateClient()
    {
        Invoice::where('id', request('id'))->update(['client_id' => request('client_id')]);
    }

    public function updateCompanyProfile()
    {
        Invoice::where('id', request('id'))->update(['company_profile_id' => request('company_profile_id')]);
    }
}