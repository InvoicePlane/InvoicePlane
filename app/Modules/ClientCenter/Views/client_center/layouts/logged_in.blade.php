@extends('client_center.layouts.master')

@section('sidebar')

    @if (config('ip.displayProfileImage'))
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ profileImageUrl(auth()->user()) }}" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p>{{ auth()->user()->name }}</p>
            </div>
        </div>
    @endif

    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('clientCenter.dashboard') }}">
                <i class="fa fa-dashboard"></i> <span>@lang('ip.dashboard')</span>
            </a>
        </li>
        <li>
            <a href="{{ route('clientCenter.quotes') }}">
                <i class="fa fa-file-text-o"></i> <span>@lang('ip.quotes')</span>
            </a>
        </li>
        <li>
            <a href="{{ route('clientCenter.invoices') }}">
                <i class="fa fa-file-text"></i> <span>@lang('ip.invoices')</span>
            </a>
        </li>
        <li>
            <a href="{{ route('clientCenter.payments') }}">
                <i class="fa fa-credit-card"></i> <span>@lang('ip.payments')</span>
            </a>
        </li>
    </ul>
@stop

@section('header')
    <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">

            <li><a href="{{ route('session.logout') }}"><i class="fa fa-power-off"></i></a></li>

        </ul>
    </div>
@stop