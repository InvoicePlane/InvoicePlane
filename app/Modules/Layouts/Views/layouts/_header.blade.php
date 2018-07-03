<header class="app-header navbar">
    <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a href="{{ route('dashboard.index')}}" class="navbar-brand">
        {{ config('fi.headerTitleText') }}
    </a>
    <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show">
        <span class="navbar-toggler-icon"></span>
    </button>
    <ul class="nav navbar-nav d-md-down-none">
        <li class="nav-item dropdown px-3">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                @lang('ip.settings')
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="{{ route('addons.index') }}">@lang('ip.addons')</a>
                <a class="dropdown-item" href="{{ route('currencies.index') }}">@lang('ip.currencies')</a>
                <a class="dropdown-item" href="{{ route('customFields.index') }}">@lang('ip.custom_fields')</a>
                <a class="dropdown-item" href="{{ route('companyProfiles.index') }}">@lang('ip.company_profiles')</a>
                <a class="dropdown-item" href="{{ route('export.index') }}">@lang('ip.export_data')</a>
                <a class="dropdown-item" href="{{ route('groups.index') }}">@lang('ip.groups')</a>
                <a class="dropdown-item" href="{{ route('import.index') }}">@lang('ip.import_data')</a>
                <a class="dropdown-item" href="{{ route('itemLookups.index') }}">@lang('ip.item_lookups')</a>
                <a class="dropdown-item" href="{{ route('mailLog.index') }}">@lang('ip.mail_log')</a>
                <a class="dropdown-item" href="{{ route('paymentMethods.index') }}">@lang('ip.payment_methods')</a>
                <a class="dropdown-item" href="{{ route('taxRates.index') }}">@lang('ip.tax_rates')</a>
                <a class="dropdown-item" href="{{ route('users.index') }}">@lang('ip.user_accounts')</a>
                <a class="dropdown-item" href="{{ route('settings.index') }}">@lang('ip.system_settings')</a>
                @foreach (config('fi.menus.system') as $menu)
                    @if (view()->exists($menu))
                        @include($menu)
                    @endif
                @endforeach
            </div>
        </li>
    </ul>
    <ul class="nav navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link nav-link pr-3" data-toggle="dropdown" href="#" role="button"
                    aria-haspopup="true" aria-expanded="false">
                @if (config('fi.displayProfileImage'))
                    <img src="{{ $profileImageUrl }}" alt="User Image"/>
                @else
                    {{ $userName }}
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-header text-center">
                    <strong>Account</strong>
                </div>
                <a class="dropdown-item" href="#">
                    <i class="fa fa-bell-o"></i> Updates
                    <span class="badge badge-info">42</span>
                </a>
                <div class="divider"></div>
                <a class="dropdown-item" href="{{ route('session.logout') }}">
                    <i class="fa fa-lock"></i> Logout
                </a>
            </div>
        </li>
    </ul>
</header>
