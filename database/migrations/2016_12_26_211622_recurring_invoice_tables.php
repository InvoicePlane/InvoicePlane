<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecurringInvoiceTables extends Migration
{
    public function up()
    {
        Schema::rename('recurring_invoices', 'recurring_invoices_old');

        Schema::create('recurring_invoices', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id');
            $table->integer('client_id');
            $table->integer('group_id');
            $table->integer('company_profile_id');
            $table->text('terms')->nullable();
            $table->text('footer')->nullable();
            $table->string('currency_code');
            $table->decimal('exchange_rate', 10, 7);
            $table->string('template');
            $table->string('summary', 100)->nullable();
            $table->decimal('discount', 15, 2);
            $table->integer('recurring_frequency');
            $table->integer('recurring_period');
            $table->date('next_date');
            $table->date('stop_date');

            $table->index('user_id');
            $table->index('client_id');
            $table->index('company_profile_id');
        });

        Schema::create('recurring_invoice_amounts', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->integer('recurring_invoice_id');
            $table->decimal('subtotal', 20, 4);
            $table->decimal('discount', 20, 4);
            $table->decimal('tax', 20, 4);
            $table->decimal('total', 20, 4);

            $table->index('recurring_invoice_id');
        });

        Schema::create('recurring_invoice_items', function(Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->integer('recurring_invoice_id');
            $table->integer('tax_rate_id')->default(0);
            $table->integer('tax_rate_2_id')->default(0);
            $table->string('name');
            $table->text('description');
            $table->decimal('quantity', 20, 4);
            $table->integer('display_order')->default(0);
            $table->decimal('price', 20, 4);

            $table->index('recurring_invoice_id');
            $table->index('tax_rate_id');
            $table->index('tax_rate_2_id');
            $table->index('display_order');
        });

        Schema::create('recurring_invoice_item_amounts', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->integer('item_id');
            $table->decimal('subtotal', 20, 4);
            $table->decimal('tax_1', 20, 4);
            $table->decimal('tax_2', 20, 4);
            $table->decimal('tax', 20, 4);
            $table->decimal('total', 20, 4);

            $table->index('item_id');
        });
    }

    public function down()
    {
        //
    }
}
