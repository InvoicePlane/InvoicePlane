<?php

namespace FI\Providers;

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
        view()->composer('layouts.master', 'FI\Composers\LayoutComposer');
        view()->composer(['client_center.layouts.master', 'client_center.layouts.public', 'layouts.master', 'setup.master'], 'FI\Composers\SkinComposer');
        view()->composer('clients._form', 'FI\Composers\ClientFormComposer');
        view()->composer('invoices._table', 'FI\Composers\InvoiceTableComposer');
        view()->composer('quotes._table', 'FI\Composers\QuoteTableComposer');
        view()->composer('reports.options.*', 'FI\Composers\ReportComposer');
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
