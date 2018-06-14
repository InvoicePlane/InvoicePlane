<?php

namespace IP\Providers;

use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('layouts.master', 'IP\Composers\LayoutComposer');
        view()->composer(['client_center.layouts.master', 'client_center.layouts.public', 'layouts.master', 'setup.master'], 'IP\Composers\SkinComposer');
        view()->composer('clients._form', 'IP\Composers\ClientFormComposer');
        view()->composer('invoices._table', 'IP\Composers\InvoiceTableComposer');
        view()->composer('quotes._table', 'IP\Composers\QuoteTableComposer');
        view()->composer('reports.options.*', 'IP\Composers\ReportComposer');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
