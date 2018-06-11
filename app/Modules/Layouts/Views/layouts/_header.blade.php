<header class="main-header">

    <a href="{{ route('dashboard.index')}}" class="logo">
        <span class="logo-lg">{{ config('fi.headerTitleText') }}</span>
    </a>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                <li>
                    <a href="https://wiki.invoiceplane.com/en/2.0/" title="{{ trans('ip.documentation') }}"
                       target="_blank">
                        <i class="fa fa-question-circle"></i>
                    </a>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="{{ trans('ip.system') }}">
                        <i class="fa fa-cog"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('addons.index') }}">{{ trans('ip.addons') }}</a></li>
                        <li><a href="{{ route('currencies.index') }}">{{ trans('ip.currencies') }}</a></li>
                        <li><a href="{{ route('customFields.index') }}">{{ trans('ip.custom_fields') }}</a></li>
                        <li><a href="{{ route('companyProfiles.index') }}">{{ trans('ip.company_profiles') }}</a></li>
                        <li><a href="{{ route('export.index') }}">{{ trans('ip.export_data') }}</a></li>
                        <li><a href="{{ route('groups.index') }}">{{ trans('ip.groups') }}</a></li>
                        <li><a href="{{ route('import.index') }}">{{ trans('ip.import_data') }}</a></li>
                        <li><a href="{{ route('itemLookups.index') }}">{{ trans('ip.item_lookups') }}</a></li>
                        <li><a href="{{ route('mailLog.index') }}">{{ trans('ip.mail_log') }}</a></li>
                        <li><a href="{{ route('paymentMethods.index') }}">{{ trans('ip.payment_methods') }}</a></li>
                        <li><a href="{{ route('taxRates.index') }}">{{ trans('ip.tax_rates') }}</a></li>
                        <li><a href="{{ route('users.index') }}">{{ trans('ip.user_accounts') }}</a></li>
                        <li><a href="{{ route('settings.index') }}">{{ trans('ip.system_settings') }}</a></li>
                        @foreach (config('fi.menus.system') as $menu)
                            @if (view()->exists($menu))
                                @include($menu)
                            @endif
                        @endforeach
                    </ul>
                </li>

                <li>
                    <a href="{{ route('session.logout') }}" title="{{ trans('ip.sign_out') }}"><i
                                class="fa fa-power-off"></i></a>
                </li>

            </ul>
        </div>
    </nav>
</header>