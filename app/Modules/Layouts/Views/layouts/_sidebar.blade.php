<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            @if (isset($displaySearch) and $displaySearch == true)
                <li class="nav-item">
                    <form class="form-inline" action="{{ request()->fullUrl() }}" method="get">
                        <input type="hidden" name="status" value="{{ request('status') }}"/>
                        <input type="text" name="search" class="form-control mr-sm-2"
                                placeholder="@lang('ip.search')..."/>
                        <button class="btn btn-outline-success my-2 my-sm-0" id="search-btn" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </form>
                </li>
            @endif
            <li class="nav-item">
                <a class="nav-link" href="{{ route('dashboard.index') }}">
                    <i class="nav-icon fa fa-dashboard"></i> <span>@lang('ip.dashboard')</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('clients.index', ['status' => 'active']) }}">
                    <i class="nav-icon fa fa-users"></i> <span>@lang('ip.clients')</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('quotes.index', ['status' => config('fi.quoteStatusFilter')]) }}">
                    <i class="nav-icon fa fa-file-text-o"></i> <span>@lang('ip.quotes')</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                        href="{{ route('invoices.index', ['status' => config('fi.invoiceStatusFilter')]) }}">
                    <i class="nav-icon fa fa-file-text"></i> <span>@lang('ip.invoices')</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('recurringInvoices.index') }}">
                    <i class="nav-icon fa fa-refresh"></i> <span>@lang('ip.recurring_invoices')</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('payments.index') }}">
                    <i class="nav-icon fa fa-credit-card"></i> <span>@lang('ip.payments')</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('expenses.index') }}">
                    <i class="nav-icon fa fa-bank"></i> <span>@lang('ip.expenses')</span>
                </a>
            </li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#">
                    <i class="nav-icon fa fa-bar-chart-o"></i> <span>@lang('ip.reports')</span>
                </a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reports.clientStatement') }}">
                            @lang('ip.client_statement')
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reports.expenseList') }}">
                            @lang('ip.expense_list')
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reports.itemSales') }}">
                            @lang('ip.item_sales')
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reports.paymentsCollected') }}">
                            @lang('ip.payments_collected')
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reports.profitLoss') }}">
                            @lang('ip.profit_and_loss')
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reports.revenueByClient') }}">
                            @lang('ip.revenue_by_client')
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reports.taxSummary') }}">
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
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
