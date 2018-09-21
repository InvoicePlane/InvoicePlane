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
                <span class="sr-only">@lang('ip.documentation')</span>
            </a>
        </li>

        <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" title="@lang('ip.system')">
                <i class="fa fa-cog"></i>
                <span class="sr-only">@lang('ip.system')</span>
            </a>
            <div class="dropdown-menu">

                <a href="{{ route('addons.index') }}" class="dropdown-item">
                    @lang('ip.addons')
                </a>
                <a href="{{ route('currencies.index') }}" class="dropdown-item">
                    @lang('ip.currencies')
                </a>
                <a href="{{ route('customFields.index') }}" class="dropdown-item">
                    @lang('ip.custom_fields')
                </a>
                <a href="{{ route('companyProfiles.index') }}" class="dropdown-item">
                    @lang('ip.company_profiles')
                </a>
                <a href="{{ route('export.index') }}" class="dropdown-item">
                    @lang('ip.export_data')
                </a>
                <a href="{{ route('groups.index') }}" class="dropdown-item">
                    @lang('ip.groups')
                </a>
                <a href="{{ route('import.index') }}" class="dropdown-item">
                    @lang('ip.import_data')
                </a>
                <a href="{{ route('itemLookups.index') }}" class="dropdown-item">
                    @lang('ip.item_lookups')
                </a>
                <a href="{{ route('mailLog.index') }}" class="dropdown-item">
                    @lang('ip.mail_log')
                </a>
                <a href="{{ route('paymentMethods.index') }}" class="dropdown-item">
                    @lang('ip.payment_methods')
                </a>
                <a href="{{ route('taxRates.index') }}" class="dropdown-item">
                    @lang('ip.tax_rates')
                </a>
                <a href="{{ route('users.index') }}" class="dropdown-item">
                    @lang('ip.user_accounts')
                </a>
                <a href="{{ route('settings.index') }}" class="dropdown-item">
                    @lang('ip.system_settings')
                </a>

                @foreach (config('ip.menus.system') as $menu)
                    @if (view()->exists($menu))
                        @include($menu)
                    @endif
                @endforeach
            </div>
        </li>

        @if (config('ip.displayProfileImage'))
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
