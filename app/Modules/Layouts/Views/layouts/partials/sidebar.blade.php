<aside class="sidebar-inner">

    <div class="sidebar-top nav">
        <div class="nav-item">
            <img src="{{ asset('assets/img/ip_logo_1x1.svg') }}"
                    alt="{{ config('fi.headerTitleText') }}" class="sidebar-top-logo">
        </div>
        <div class="nav-item">
            <a href="{{ route('dashboard.index') }}" class="nav-link">

                {{ config('fi.headerTitleText') }}
            </a>
        </div>
        <a href="#" class="sidebar-toggle ml-auto nav-link">
            <i class="fa fa-close"></i>
        </a>
    </div>

    <ul class="menu">

        @if (isset($displaySearch) and $displaySearch == true)
            <li>
                <form action="{{ request()->fullUrl() }}" method="get" class="sidebar-form">
                    <input type="hidden" name="status" value="{{ request('status') }}"/>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control"
                                placeholder="@lang('ip.search')..."/>
                        <div class="input-group-btn">
                            <button type="submit" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </li>
        @endif

        <li>
            <a href="{{ route('dashboard.index') }}">
                <i class="fa fa-fw fa-dashboard"></i> <span>@lang('ip.dashboard')</span>
            </a>
        </li>
        <li>
            <a href="{{ route('clients.index', ['status' => 'active']) }}">
                <i class="fa fa-fw fa-users"></i> <span>@lang('ip.clients')</span>
            </a>
        </li>
        <li>
            <a href="{{ route('quotes.index', ['status' => config('fi.quoteStatusFilter')]) }}">
                <i class="fa fa-fw fa-file-text-o"></i> <span>@lang('ip.quotes')</span>
            </a>
        </li>
        <li>
            <a href="{{ route('invoices.index', ['status' => config('fi.invoiceStatusFilter')]) }}">
                <i class="fa fa-fw fa-file-text"></i> <span>@lang('ip.invoices')</span>
            </a>
        </li>
        <li>
            <a href="{{ route('recurringInvoices.index') }}">
                <i class="fa fa-fw fa-refresh"></i> <span>@lang('ip.recurring_invoices')</span>
            </a>
        </li>
        <li>
            <a href="{{ route('payments.index') }}">
                <i class="fa fa-fw fa-credit-card"></i> <span>@lang('ip.payments')</span>
            </a>
        </li>
        <li>
            <a href="{{ route('expenses.index') }}">
                <i class="fa fa-fw fa-bank"></i> <span>@lang('ip.expenses')</span>
            </a>
        </li>

        <li>
            <a href="#" data-toggle="collapse" data-target="#submenu-reports" class="has-submenu collapsed">
                <i class="fa fa-fw fa-bar-chart-o"></i> @lang('ip.reports')
                <span class="pull-right"><span class="menu-icon fa fa-fw"></span></span>
            </a>
            <ul id="submenu-reports" class="submenu collapse">
                <li>
                    <a href="{{ route('reports.clientStatement') }}">
                        @lang('ip.client_statement')
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.expenseList') }}">
                        @lang('ip.expense_list')
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.itemSales') }}">
                        @lang('ip.item_sales')
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.paymentsCollected') }}">
                        @lang('ip.payments_collected')
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.profitLoss') }}">
                        @lang('ip.profit_and_loss')
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.revenueByClient') }}">
                        @lang('ip.revenue_by_client')
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.taxSummary') }}">
                        @lang('ip.tax_summary')
                    </a>
                </li>

                @foreach (config('fi.menus.reports') as $report)
                    @if (view()->exists($report))
                        @include($report)
                    @endif
                @endforeach
            </ul>
        </li>

        @foreach (config('fi.menus.navigation') as $menu)
            @if (view()->exists($menu))
                @include($menu)
            @endif
        @endforeach

    </ul>
</aside>
