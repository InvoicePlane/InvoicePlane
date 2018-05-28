<?php

use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Quotes\Models\Quote;
use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Currency extends Migration
{
    public function up()
    {
        $baseCurrency = Setting::where('setting_key', 'baseCurrency')->first()->setting_value;

        // There may be some records with null currency_code values so we need to update these
        DB::table('clients')->whereNull('currency_code')->update(['currency_code' => $baseCurrency]);

        $invoices = Invoice::with('client')->whereNull('currency_code')->get();

        foreach ($invoices as $invoice)
        {
            $invoice->currency_code = $invoice->client->currency_code;
            $invoice->save();
        }

        $quotes = Quote::with('client')->whereNull('currency_code')->get();

        foreach ($quotes as $quote)
        {
            $quote->currency_code = $quote->client->currency_code;
            $quote->save();
        }
    }

    public function down()
    {
        //
    }
}
