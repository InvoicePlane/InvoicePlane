<header id="topbar">

    <div class="nav sidebar-toggle-nav mr-auto">
        <div class="nav-item">
            <a href="#" class="sidebar-toggle nav-link">
                <i class="fa fa-bars fa-margin-right"></i> @lang('ip.menu')
            </a>
        </div>
    </div>

    <ul class="nav ml-auto">

        <li class="nav-item">
            <a href="https://wiki.invoiceplane.com/en/2.0/" title="@lang('ip.documentation')"
                    target="_blank" class="nav-link">
                <i class="fa fa-question-circle"></i>
            </a>
        </li>

        <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" title="@lang('ip.system')">
                <i class="fa fa-cog"></i>
            </a>
            <ul class="dropdown-menu">
                <li><a href="{{ route('addons.index') }}">@lang('ip.addons')</a></li>
                <li><a href="{{ route('currencies.index') }}">@lang('ip.currencies')</a></li>
                <li><a href="{{ route('customFields.index') }}">@lang('ip.custom_fields')</a></li>
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

        @if (config('fi.displayProfileImage'))
            <li class="nav-item">
                <img src="{{ $profileImageUrl }}" alt="{{ $userName }}" class="user-image"/>
                {{ $userName }}
            </li>
        @endif

        <li class="nav-item">
            <a href="{{ route('session.logout') }}" title="@lang('ip.sign_out')" class="nav-link">
                <i class="fa fa-power-off"></i>
            </a>
        </li>

    </ul>

</header>
