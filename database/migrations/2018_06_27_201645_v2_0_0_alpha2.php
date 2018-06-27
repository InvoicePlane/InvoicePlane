<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use IP\Modules\Currencies\Models\Currency;
use IP\Modules\Settings\Models\Setting;
use IP\Modules\PaymentMethods\Models\PaymentMethod;

/**
 * Migration for Version 2.0.0-Alpha.2
 *
 * This migration provides information about the current state of the
 * application with all tables and the most critical seed data.
 * to be backwards compatible with FusionInvoice this migration
 * checks if the activities tables exist (which must be present in
 * FusionInvoice 2018-8) and just clears the cache and updates the
 * version then.
 * For new users this migration will create and fill all base tables.
 */
class V200Alpha2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('activities')) {
            // Stop migration of the base tables if they already exist
            // (e.g. if user upgrades from FusionInvoice to InvoicePlane 2)

            // Run the basic update before
            Setting::saveByKey('version', '2.0.0-alpha2');
            deleteTempFiles();
            deleteViewCache();

            return;
        }

        Schema::create('activities', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('audit_type');
            $table->string('activity');
            $table->integer('audit_id');
            $table->text('info')->nullable();

            $table->index('audit_type');
            $table->index('activity');
            $table->index('parent_id');
        });

        Schema::create('addons', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->string('author_name');
            $table->string('author_url');
            $table->longText('navigation_menu')->nullable();
            $table->longText('system_menu')->nullable();
            $table->longText('navigation_reports')->nullable();
            $table->string('path');
            $table->boolean('enabled')->default(0);
        });

        Schema::create('attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id')->unsigned();
            $table->integer('attachable_id')->unsigned();
            $table->string('attachable_type');
            $table->string('filename');
            $table->string('mimetype');
            $table->integer('size');
            $table->string('url_key');
            $table->integer('client_visibility');
        });

        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('web')->nullable();
            $table->string('url_key');
            $table->boolean('active')->default(1);
            $table->string('currency_code')->nullable();
            $table->string('unique_name')->nullable();
            $table->string('language')->nullable();

            $table->index('name');
            $table->index('active');
        });

        Schema::create('clients_custom', function (Blueprint $table) {
            $table->integer('client_id');
            $table->timestamps();

            $table->primary('client_id');
        });

        Schema::create('company_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('company')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('mobile')->nullable();
            $table->string('web')->nullable();
            $table->string('logo')->nullable();
            $table->string('quote_template');
            $table->string('invoice_template');
        });

        Schema::create('company_profiles_custom', function (Blueprint $table) {
            $table->integer('company_profile_id');
            $table->timestamps();

            $table->primary('company_profile_id');
        });

        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('client_id');
            $table->string('name');
            $table->string('email');
            $table->boolean('default_to');
            $table->boolean('default_cc');
            $table->boolean('default_bcc');

            $table->index('client_id');
        });

        Schema::create('currencies', function (Blueprint $table) {
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

        Schema::create('custom_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('tbl_name');
            $table->string('column_name');
            $table->string('field_label');
            $table->string('field_type');
            $table->text('field_meta');

            $table->index('table_name');
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->date('expense_date');
            $table->integer('user_id');
            $table->integer('category_id');
            $table->integer('client_id');
            $table->integer('vendor_id');
            $table->integer('invoice_id');
            $table->string('description')->nullable();
            $table->string('amount');

            $table->index('category_id');
            $table->index('client_id');
            $table->index('vendor_id');
            $table->index('invoice_id');
        });

        Schema::create('expense_vendors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('expense_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('expenses_custom', function (Blueprint $table) {
            $table->integer('expense_id');
            $table->timestamps();

            $table->primary('expense_id');
        });

        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->integer('next_id')->default(1);
            $table->integer('left_pad')->default(0);
            $table->string('format');
            $table->integer('reset_number');
            $table->integer('last_id');
            $table->integer('last_year');
            $table->integer('last_month');
            $table->integer('last_week');
        });

        Schema::create('invoice_amounts', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('invoice_id');
            $table->decimal('subtotal', 20, 4)->default(0.00);
            $table->decimal('discount', 20, 4)->default(0.00);
            $table->decimal('tax', 20, 4)->default(0.00);
            $table->decimal('total', 20, 4)->default(0.00);
            $table->decimal('paid', 20, 4)->default(0.00);
            $table->decimal('balance', 20, 4)->default(0.00);

            $table->index('invoice_id');
        });

        Schema::create('invoice_item_amounts', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('item_id');
            $table->decimal('subtotal', 20, 4)->default(0.00);
            $table->decimal('tax_1', 20, 4)->default(0.00);
            $table->decimal('tax_2', 20, 4)->default(0.00);
            $table->decimal('tax', 20, 4)->default(0.00);
            $table->decimal('total', 20, 4)->default(0.00);

            $table->index('item_id');
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('invoice_id');
            $table->integer('tax_rate_id');
            $table->integer('tax_rate_2_id');
            $table->string('name');
            $table->text('description');
            $table->decimal('quantity', 20, 4)->default(0.00);
            $table->decimal('price', 20, 4)->default(0.00);
            $table->integer('display_order')->default(0);

            $table->index('invoice_id');
            $table->index('tax_rate_id');
            $table->index('display_order');
        });

        Schema::create('invoice_tax_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('invoice_id');
            $table->integer('tax_rate_id');
            $table->boolean('include_item_tax')->default(0);
            $table->decimal('tax_total', 15, 2)->default(0.00);

            $table->index('invoice_id');
            $table->index('tax_rate_id');
        });

        Schema::create('invoice_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('invoice_id');
            $table->boolean('is_successful');
            $table->string('transaction_reference')->nullable();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->date('invoice_date');
            $table->integer('user_id');
            $table->integer('client_id');
            $table->integer('group_id');
            $table->integer('invoice_status_id');
            $table->date('due_at');
            $table->string('number');
            $table->text('terms')->nullable();
            $table->text('footer')->nullable();
            $table->string('url_key');
            $table->string('currency_code')->nullable();
            $table->decimal('exchange_rate', 10, 7)->default(0.00);
            $table->string('template')->nullable();
            $table->string('summary')->nullable();
            $table->boolean('viewed')->default(0);
            $table->decimal('discount', 15, 2)->default(0.00);
            $table->integer('company_profile_id');

            $table->index('user_id');
            $table->index('client_id');
            $table->index('group_id');
            $table->index('invoice_status_id');
            $table->index('company_profile_id');
        });

        Schema::create('invoices_custom', function (Blueprint $table) {
            $table->integer('invoice_id');
            $table->timestamps();

            $table->primary('invoice_id');
        });

        Schema::create('item_lookups', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 20, 4)->default(0.00);
            $table->integer('tax_rate_id')->default(0);
            $table->integer('tax_rate_2_id')->default(0);

            $table->index('tax_rate_id');
            $table->index('tax_rate_2_id');
        });

        Schema::create('mail_queue', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('mailable_id');
            $table->string('mailable_type');
            $table->string('from');
            $table->string('to');
            $table->string('cc');
            $table->string('bcc');
            $table->string('subject');
            $table->longText('body');
            $table->boolean('attach_pdf');
            $table->boolean('sent');
            $table->text('error')->nullable();
        });

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

        Schema::create('notes', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id');
            $table->integer('notable_id');
            $table->string('notable_type');
            $table->longText('note');
            $table->boolean('private');
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('invoice_id');
            $table->integer('payment_method_id');
            $table->date('paid_at');
            $table->text('note');
            $table->decimal('amount', 20, 4)->default(0.00);

            $table->index('invoice_id');
            $table->index('payment_method_id');
            $table->index('amount');
        });

        Schema::create('payments_custom', function (Blueprint $table) {
            $table->integer('payment_id');
            $table->timestamps();

            $table->primary('payment_id');
        });

        Schema::create('quote_amounts', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('quote_id');
            $table->decimal('subtotal', 20, 4)->default(0.00);
            $table->decimal('discount', 20, 4)->default(0.00);
            $table->decimal('tax', 20, 4)->default(0.00);
            $table->decimal('total', 20, 4)->default(0.00);

            $table->index('quote_id');
        });

        Schema::create('quote_item_amounts', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('item_id');
            $table->decimal('subtotal', 20, 4)->default(0.00);
            $table->decimal('tax_1', 20, 4)->default(0.00);
            $table->decimal('tax_2', 20, 4)->default(0.00);
            $table->decimal('tax', 20, 4)->default(0.00);
            $table->decimal('total', 20, 4)->default(0.00);

            $table->index('item_id');
        });

        Schema::create('quote_items', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('quote_id');
            $table->integer('tax_rate_id');
            $table->integer('tax_rate_2_id');
            $table->string('name');
            $table->text('description');
            $table->decimal('quantity', 20, 4)->default(0.00);
            $table->integer('display_order');
            $table->decimal('price', 20, 4)->default(0.00);

            $table->index('quote_id');
            $table->index('display_order');
            $table->index('tax_rate_id');
        });

        Schema::create('quote_tax_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('quote_id');
            $table->integer('tax_rate_id');
            $table->boolean('include_item_tax')->default(0);
            $table->decimal('tax_total', 20, 4)->default(0.00);

            $table->index('quote_id');
            $table->index('tax_rate_id');
        });

        Schema::create('quotes', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->date('quote_date');
            $table->integer('invoice_id')->default('0');
            $table->integer('user_id');
            $table->integer('client_id');
            $table->integer('group_id');
            $table->integer('quote_status_id');
            $table->date('expires_at');
            $table->string('number');
            $table->text('footer')->nullable();
            $table->string('url_key');
            $table->string('currency_code')->nullable();
            $table->decimal('exchange_rate', 10, 7)->default(0.00);
            $table->text('terms')->nullable();
            $table->string('template')->nullable();
            $table->string('summary')->nullable();
            $table->boolean('viewed')->default(0);
            $table->decimal('discount', 15, 2)->default(0.00);
            $table->integer('company_profile_id');

            $table->index('user_id');
            $table->index('client_id');
            $table->index('invoice_group_id');
            $table->index('number');
            $table->index('company_profile_id');
        });

        Schema::create('quotes_custom', function (Blueprint $table) {
            $table->integer('quote_id');
            $table->timestamps();

            $table->primary('quote_id');
        });

        Schema::create('recurring_invoice_amounts', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('recurring_invoice_id');
            $table->decimal('subtotal', 20, 4);
            $table->decimal('discount', 20, 4);
            $table->decimal('tax', 20, 4);
            $table->decimal('total', 20, 4);

            $table->index('recurring_invoice_id');
        });

        Schema::create('recurring_invoice_item_amounts', function (Blueprint $table) {
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

        Schema::create('recurring_invoice_items', function (Blueprint $table) {
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

        Schema::create('recurring_invoices', function (Blueprint $table) {
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
            $table->string('summary')->nullable();
            $table->decimal('discount', 15, 2);
            $table->integer('recurring_frequency');
            $table->integer('recurring_period');
            $table->date('next_date');
            $table->date('stop_date');

            $table->index('user_id');
            $table->index('client_id');
            $table->index('company_profile_id');
        });

        Schema::create('recurring_invoices_custom', function (Blueprint $table) {
            $table->integer('recurring_invoice_id');
            $table->timestamps();

            $table->primary('recurring_invoice_id');
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('setting_key');
            $table->text('setting_value');

            $table->index('setting_key');
        });

        Schema::create('tax_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->decimal('percent', 5, 2)->default(0.00);
            $table->boolean('is_compound')->default(0);
            $table->boolean('calculate_vat')->default(0);
        });

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('email');
            $table->string('password');
            $table->string('name');
            $table->string('remember_token', 100)->nullable();
            $table->string('api_public_key')->nullable();
            $table->string('api_secret_key')->nullable();
            $table->integer('client_id')->nullable();

            $table->index('client_id');
        });

        Schema::create('users_custom', function (Blueprint $table) {
            $table->integer('user_id');
            $table->timestamps();

            $table->primary('user_id');
        });

        // =========================================================
        // Migrate Base Data

        // Add basic voucher groups
        DB::table('invoice_groups')->insert(
            [
                'name' => trans('ip.invoice_default'),
                'next_id' => 1,
                'left_pad' => 0,
                'prefix' => 'INV',
                'prefix_year' => 0,
                'prefix_month' => 0,
            ]
        );

        DB::table('invoice_groups')->insert(
            [
                'name' => trans('ip.quote_default'),
                'next_id' => 1,
                'left_pad' => 0,
                'prefix' => 'QUO',
                'prefix_year' => 0,
                'prefix_month' => 0,
            ]
        );

        // Add base settings
        $settings = [
            'attachPdf' => '1',
            'automaticEmailOnRecur' => '1',
            'dateFormat' => 'm/d/Y',
            'headerTitleText' => 'InvoicePlane',
            'invoiceGroup' => '1',
            'invoiceTemplate' => 'default.blade.php',
            'invoicesDueAfter' => '30',
            'language' => 'en',
            'markInvoicesSentPdf' => '0',
            'markQuotesSentPdf' => '0',
            'quoteGroup' => '2',
            'quoteTemplate' => 'default.blade.php',
            'quotesExpireAfter' => '15',
            'skin' => 'skin-invoiceplane.min.css',
            'timezone' => 'UTC',
            'version' => '2.0.0-alpha2',
        ];

        foreach ($settings as $key => $value) {
            Setting::saveByKey($key, $value);
        }

        // Add base payment methods
        PaymentMethod::create(['name' => trans('fi.cash')]);
        PaymentMethod::create(['name' => trans('fi.credit_card')]);
        PaymentMethod::create(['name' => trans('fi.online_payment')]);

        // Save new currencies
        Currency::create([
            'name' => 'Australian Dollar',
            'code' => 'AUD',
            'symbol' => '$',
            'placement' => 'before',
            'decimal' => '.',
            'thousands' => ',',
        ]);

        Currency::create([
            'name' => 'Canadian Dollar',
            'code' => 'CAD',
            'symbol' => '$',
            'placement' => 'before',
            'decimal' => '.',
            'thousands' => ',',
        ]);

        Currency::create([
            'name' => 'Euro',
            'code' => 'EUR',
            'symbol' => '€',
            'placement' => 'before',
            'decimal' => '.',
            'thousands' => ',',
        ]);

        Currency::create([
            'name' => 'Pound Sterling',
            'code' => 'GBP',
            'symbol' => '£',
            'placement' => 'before',
            'decimal' => '.',
            'thousands' => ',',
        ]);

        Currency::create([
            'name' => 'US Dollar',
            'code' => 'USD',
            'symbol' => '$',
            'placement' => 'before',
            'decimal' => '.',
            'thousands' => ',',
        ]);

        Setting::saveByKey('baseCurrency', 'USD');
        Setting::saveByKey('exchangeRateMode', 'automatic');

        // Cleanup
        deleteTempFiles();
        deleteViewCache();
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
