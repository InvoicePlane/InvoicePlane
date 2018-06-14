<?php

use IP\Modules\Clients\Models\Client;
use IP\Modules\Invoices\Models\Invoice;
use IP\Modules\Quotes\Models\Quote;
use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class DefaultTemplates extends Migration
{
    public function up()
    {
        Client::whereNull('invoice_template')->update(['invoice_template' => Setting::getByKey('invoiceTemplate')]);
        Client::whereNull('quote_template')->update(['quote_template' => Setting::getByKey('quoteTemplate')]);

        $invoiceSubquery = '(' . DB::table('clients')->select('invoice_template')->where('clients.id', DB::raw(DB::getTablePrefix() . 'invoices.id'))->toSql() . ')';
        $quoteSubquery = '(' . DB::table('clients')->select('quote_template')->where('clients.id', DB::raw(DB::getTablePrefix() . 'quotes.id'))->toSql() . ')';

        Invoice::whereNull('template')->update(['template' => DB::raw($invoiceSubquery)]);
        Quote::whereNull('template')->update(['template' => DB::raw($quoteSubquery)]);
    }

    public function down()
    {
        //
    }
}
