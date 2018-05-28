<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class Install extends Migration
{
    public function up()
    {
        Schema::create('clients', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('web')->nullable();
            $table->string('url_key');
            $table->boolean('active')->default(1);

            $table->index('name');
            $table->index('active');
        });

        Schema::create('clients_custom', function (Blueprint $table)
        {
            $table->integer('client_id');
            $table->timestamps();

            $table->primary('client_id');
        });

        Schema::create('custom_fields', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->string('table_name');
            $table->string('column_name');
            $table->string('field_label');
            $table->string('field_type');
            $table->text('field_meta');

            $table->index('table_name');
        });

        Schema::create('invoices', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id');
            $table->integer('client_id');
            $table->integer('invoice_group_id');
            $table->integer('invoice_status_id');
            $table->date('due_at');
            $table->string('number');
            $table->text('terms')->nullable();
            $table->text('footer')->nullable();
            $table->string('url_key');

            $table->index('user_id');
            $table->index('client_id');
            $table->index('invoice_group_id');
            $table->index('invoice_status_id');
        });

        Schema::create('invoices_custom', function (Blueprint $table)
        {
            $table->integer('invoice_id');
            $table->timestamps();

            $table->primary('invoice_id');
        });

        Schema::create('invoice_amounts', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->integer('invoice_id');
            $table->decimal('item_subtotal', 10, 2)->default(0.00);
            $table->decimal('item_tax_total', 10, 2)->default(0.00);
            $table->decimal('tax_total', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2)->default(0.00);
            $table->decimal('paid', 10, 2)->default(0.00);
            $table->decimal('balance', 10, 2)->default(0.00);

            $table->index('invoice_id');
        });

        Schema::create('invoice_groups', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->string('prefix');
            $table->integer('next_id')->default(1);
            $table->integer('left_pad')->default(0);
            $table->boolean('prefix_year')->default(0);
            $table->boolean('prefix_month')->default(0);
        });

        Schema::create('invoice_items', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->integer('invoice_id');
            $table->integer('tax_rate_id');
            $table->string('name');
            $table->text('description');
            $table->decimal('quantity', 10, 2)->default(0.00);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->integer('display_order')->default(0);

            $table->index('invoice_id');
            $table->index('tax_rate_id');
            $table->index('display_order');
        });

        Schema::create('invoice_item_amounts', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->integer('item_id');
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('tax_total', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2)->default(0.00);

            $table->index('item_id');
        });

        Schema::create('invoice_tax_rates', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->integer('invoice_id');
            $table->integer('tax_rate_id');
            $table->boolean('include_item_tax')->default(0);
            $table->decimal('tax_total', 10, 2)->default(0.00);

            $table->index('invoice_id');
            $table->index('tax_rate_id');
        });

        Schema::create('item_lookups', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2)->default(0.00);
        });

        Schema::create('payments', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->integer('invoice_id');
            $table->integer('payment_method_id');
            $table->date('paid_at');
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->text('note');

            $table->index('invoice_id');
            $table->index('payment_method_id');
            $table->index('amount');
        });

        Schema::create('payments_custom', function (Blueprint $table)
        {
            $table->integer('payment_id');
            $table->timestamps();

            $table->primary('payment_id');
        });

        Schema::create('payment_methods', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
        });

        Schema::create('quotes', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->integer('invoice_id')->default('0');
            $table->integer('user_id');
            $table->integer('client_id');
            $table->integer('invoice_group_id');
            $table->integer('quote_status_id');
            $table->date('expires_at');
            $table->string('number');
            $table->text('footer')->nullable();
            $table->string('url_key');

            $table->index('user_id');
            $table->index('client_id');
            $table->index('invoice_group_id');
            $table->index('number');
        });

        Schema::create('quotes_custom', function (Blueprint $table)
        {
            $table->integer('quote_id');
            $table->timestamps();

            $table->primary('quote_id');
        });

        Schema::create('quote_amounts', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->integer('quote_id');
            $table->decimal('item_subtotal', 10, 2)->default(0.00);
            $table->decimal('item_tax_total', 10, 2)->default(0.00);
            $table->decimal('tax_total', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2)->default(0.00);

            $table->index('quote_id');
        });

        Schema::create('quote_items', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->integer('quote_id');
            $table->integer('tax_rate_id');
            $table->string('name');
            $table->text('description');
            $table->decimal('quantity', 10, 2)->default(0.00);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->integer('display_order');

            $table->index('quote_id');
            $table->index('display_order');
            $table->index('tax_rate_id');
        });

        Schema::create('quote_item_amounts', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->integer('item_id');
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('tax_total', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2)->default(0.00);

            $table->index('item_id');
        });

        Schema::create('quote_tax_rates', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->integer('quote_id');
            $table->integer('tax_rate_id');
            $table->boolean('include_item_tax')->default(0);
            $table->decimal('tax_total', 10, 2)->default(0.00);

            $table->index('quote_id');
            $table->index('tax_rate_id');
        });

        Schema::create('recurring_invoices', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->integer('invoice_id');
            $table->integer('recurring_frequency');
            $table->integer('recurring_period');
            $table->dateTime('generate_at');
        });

        Schema::create('settings', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->string('setting_key');
            $table->text('setting_value');

            $table->index('setting_key');
        });

        Schema::create('tax_rates', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->decimal('percent', 5, 2)->default(0.00);
        });

        Schema::create('users', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->string('email');
            $table->string('password');
            $table->string('name');
            $table->string('company')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('mobile')->nullable();
            $table->string('web')->nullable();
        });

        DB::table('invoice_groups')->insert(
            [
                'name'         => trans('fi.invoice_default'),
                'next_id'      => 1,
                'left_pad'     => 0,
                'prefix'       => 'INV',
                'prefix_year'  => 0,
                'prefix_month' => 0,
            ]
        );

        DB::table('invoice_groups')->insert(
            [
                'name'         => trans('fi.quote_default'),
                'next_id'      => 1,
                'left_pad'     => 0,
                'prefix'       => 'QUO',
                'prefix_year'  => 0,
                'prefix_month' => 0,
            ]
        );

        $settings = [
            'language'              => 'en',
            'dateFormat'            => 'm/d/Y',
            'invoiceTemplate'       => 'default.blade.php',
            'invoicesDueAfter'      => 30,
            'invoiceGroup'          => 1,
            'quoteTemplate'         => 'default.blade.php',
            'quotesExpireAfter'     => 15,
            'quoteGroup'            => 2,
            'automaticEmailOnRecur' => 1,
            'markInvoicesSentPdf'   => 0,
            'markQuotesSentPdf'     => 0,
            'timezone'              => 'America/Phoenix',
            'attachPdf'             => 1,
        ];

        foreach ($settings as $key => $value)
        {
            Setting::saveByKey($key, $value);
        }
    }

    public function down()
    {
        //
    }
}