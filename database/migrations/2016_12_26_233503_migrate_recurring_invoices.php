<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MigrateRecurringInvoices extends Migration
{
    public function up()
    {
        $invoices = DB::table('recurring_invoices_old')
            ->join('invoices', 'invoices.id', '=', 'recurring_invoices_old.invoice_id')
            ->get();

        foreach ($invoices as $invoice)
        {
            $recurringInvoiceId = DB::table('recurring_invoices')
                ->insertGetId([
                    'created_at'          => $invoice->created_at,
                    'updated_at'          => $invoice->updated_at,
                    'user_id'             => $invoice->user_id,
                    'client_id'           => $invoice->client_id,
                    'group_id'            => $invoice->group_id,
                    'company_profile_id'  => $invoice->company_profile_id,
                    'terms'               => $invoice->terms,
                    'footer'              => $invoice->footer,
                    'currency_code'       => $invoice->currency_code,
                    'exchange_rate'       => $invoice->exchange_rate,
                    'template'            => $invoice->template,
                    'summary'             => $invoice->summary,
                    'discount'            => $invoice->discount,
                    'recurring_frequency' => $invoice->recurring_frequency,
                    'recurring_period'    => $invoice->recurring_period,
                    'next_date'           => $invoice->generate_at,
                    'stop_date'           => $invoice->stop_at,
                ]);

            $invoiceAmount = DB::table('invoice_amounts')
                ->where('invoice_id', $invoice->id)
                ->first();

            DB::table('recurring_invoice_amounts')
                ->insert([
                    'created_at'           => $invoiceAmount->created_at,
                    'updated_at'           => $invoiceAmount->updated_at,
                    'recurring_invoice_id' => $recurringInvoiceId,
                    'subtotal'             => $invoiceAmount->subtotal,
                    'discount'             => $invoiceAmount->discount,
                    'tax'                  => $invoiceAmount->tax,
                    'total'                => $invoiceAmount->total,
                ]);

            $items = DB::table('invoice_items')
                ->where('invoice_id', $invoice->id)
                ->get();

            foreach ($items as $item)
            {
                $invoiceItemId = DB::table('recurring_invoice_items')
                    ->insertGetId([
                        'created_at'           => $item->created_at,
                        'updated_at'           => $item->updated_at,
                        'recurring_invoice_id' => $recurringInvoiceId,
                        'tax_rate_id'          => $item->tax_rate_id,
                        'tax_rate_2_id'        => $item->tax_rate_2_id,
                        'name'                 => $item->name,
                        'description'          => $item->description,
                        'quantity'             => $item->quantity,
                        'display_order'        => $item->display_order,
                        'price'                => $item->price,
                    ]);

                $itemAmount = DB::table('invoice_item_amounts')
                    ->where('invoice_item_amounts.item_id', '=', $item->id)
                    ->first();

                DB::table('recurring_invoice_item_amounts')
                    ->insert([
                        'created_at' => $itemAmount->created_at,
                        'updated_at' => $itemAmount->updated_at,
                        'item_id'    => $invoiceItemId,
                        'subtotal'   => $itemAmount->subtotal,
                        'tax_1'      => $itemAmount->tax_1,
                        'tax_2'      => $itemAmount->tax_2,
                        'tax'        => $itemAmount->tax,
                        'total'      => $itemAmount->total,
                    ]);
            }
        }
    }

    public function down()
    {
        //
    }
}
