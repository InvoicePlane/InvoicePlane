<?php

use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Quotes\Models\Quote;
use Illuminate\Database\Migrations\Migration;

class Update extends Migration
{
    public function up()
    {
        Invoice::whereNotIn('id', function ($query) {
            $query->select('invoice_id')->from('invoice_amounts');
        })->delete();

        Quote::whereNotIn('id', function ($query) {
            $query->select('invoice_id')->from('quote_amounts');
        })->delete();
    }

    public function down()
    {
        //
    }
}