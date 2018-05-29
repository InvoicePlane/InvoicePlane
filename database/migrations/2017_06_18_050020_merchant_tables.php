<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MerchantTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_clients', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('driver');
            $table->integer('client_id');
            $table->string('merchant_key');
            $table->string('merchant_value');

            $table->index('driver');
            $table->index('client_id');
            $table->index('merchant_key');
        });

        Schema::create('merchant_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('driver');
            $table->integer('payment_id');
            $table->string('merchant_key');
            $table->string('merchant_value');

            $table->index('driver');
            $table->index('payment_id');
            $table->index('merchant_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
