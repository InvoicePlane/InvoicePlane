<?php

use FI\Modules\Currencies\Models\Currency;
use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class Currencies extends Migration
{
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->string('code');
            $table->string('name');
            $table->string('symbol');
            $table->string('placement');
            $table->string('decimal');
            $table->string('thousands');

            $table->index('name');
        });

        Schema::table('clients', function (Blueprint $table)
        {
            $table->string('currency_code')->nullable();
        });

        Schema::table('invoices', function (Blueprint $table)
        {
            $table->string('currency_code')->nullable();
            $table->decimal('exchange_rate', 10, 7)->default('1');
        });

        Schema::table('quotes', function (Blueprint $table)
        {
            $table->string('currency_code')->nullable();
            $table->decimal('exchange_rate', 10, 7)->default('1');
        });

        Currency::create(['name' => 'Australian Dollar', 'code' => 'AUD', 'symbol' => '$', 'placement' => 'before', 'decimal' => '.', 'thousands' => ',']);
        Currency::create(['name' => 'Canadian Dollar', 'code' => 'CAD', 'symbol' => '$', 'placement' => 'before', 'decimal' => '.', 'thousands' => ',']);
        Currency::create(['name' => 'Euro', 'code' => 'EUR', 'symbol' => '€', 'placement' => 'before', 'decimal' => '.', 'thousands' => ',']);
        Currency::create(['name' => 'Pound Sterling', 'code' => 'GBP', 'symbol' => '£', 'placement' => 'before', 'decimal' => '.', 'thousands' => ',']);
        Currency::create(['name' => 'US Dollar', 'code' => 'USD', 'symbol' => '$', 'placement' => 'before', 'decimal' => '.', 'thousands' => ',']);

        Setting::saveByKey('baseCurrency', 'USD');
        Setting::saveByKey('exchangeRateMode', 'automatic');
    }

    public function down()
    {
        //
    }
}
