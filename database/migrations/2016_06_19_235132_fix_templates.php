<?php

use FI\Modules\Clients\Models\Client;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Quotes\Models\Quote;
use Illuminate\Database\Migrations\Migration;

class FixTemplates extends Migration
{
    public function up()
    {
        Client::whereNull('invoice_template')->orWhere('invoice_template', '')->update(['invoice_template' => config('fi.invoiceTemplate')]);
        Client::whereNull('quote_template')->orWhere('quote_template', '')->update(['quote_template' => config('fi.quoteTemplate')]);
        Invoice::whereNull('template')->orWhere('template', '')->update(['template' => config('fi.invoiceTemplate')]);
        Quote::whereNull('template')->orWhere('template', '')->update(['template' => config('fi.quoteTemplate')]);
    }

    public function down()
    {
        //
    }
}
