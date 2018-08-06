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
                    <a href="https://wiki.invoiceplane.com/en/2.0/" title="@lang('ip.documentation')"
                       target="_blank">
                        <i class="fa fa-question-circle"></i>
                    </a>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="@lang('ip.system')">
                        <i class="fa fa-cog"></i>
                    </a>
                    <ul class="dropdown-menu">
                        {{--<li><a href="{{ route('addons.index') }}">@lang('ip.addons')</a></li>--}}
                        <li><a href="{{ route('currencies.index') }}">@lang('ip.currencies')</a></li>
                        {{--<li><a href="{{ route('customFields.index') }}">@lang('ip.custom_fields')</a></li>--}}
                        <li><a href="{{ route('companyProfiles.index') }}">@lang('ip.company_profiles')</a></li>
                        <li><a href="{{ route('export.index') }}">@lang('ip.export_data')</a></li>
                        <li><a href="{{ route('groups.index') }}">@lang('ip.groups')</a></li>
                        <li><a href="{{ route('import.index') }}">@lang('ip.import_data')</a></li>
                        <li><a href="{{ route('itemLookups.index') }}">@lang('ip.item_lookups')</a></li>
                        <li><a href="{{ route('mailLog.index') }}">@lang('ip.mail_log')</a></li>
                        <li><a href="{{ route('paymentMethods.index') }}">@lang('ip.payment_methods')</a></li>
                        <li><a href="{{ route('taxRates.index') }}">@lang('ip.tax_rates')</a></li>
                        <li><a href="{{ route('users.index') }}">@lang('ip.user_accounts')</a></li>
                        <li><a href="{{ route('settings.index') }}">@lang('ip.system_settings')</a></li>
                        @foreach (config('fi.menus.system') as $menu)
                            @if (view()->exists($menu))
                                @include($menu)
                            @endif
                        @endforeach
                    </ul>
                </li>

                <li>
                    <a href="{{ route('session.logout') }}" title="@lang('ip.sign_out')"><i
                                class="fa fa-power-off"></i></a>
                </li>

            </ul>
        </div>
    </nav>
</header>