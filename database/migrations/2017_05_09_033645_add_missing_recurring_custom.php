<?php

use FI\Modules\CustomFields\Models\RecurringInvoiceCustom;
use FI\Modules\RecurringInvoices\Models\RecurringInvoice;
use Illuminate\Database\Migrations\Migration;

class AddMissingRecurringCustom extends Migration
{
    public function up()
    {
        $recurringInvoices = RecurringInvoice::whereNotIn('id', function ($query)
        {
            $query->select('id')->from('recurring_invoices_custom');
        })->get();

        foreach ($recurringInvoices as $recurringInvoice)
        {
            $recurringInvoice->custom()->save(new RecurringInvoiceCustom());
        }
    }

    public function down()
    {
        //
    }
}
