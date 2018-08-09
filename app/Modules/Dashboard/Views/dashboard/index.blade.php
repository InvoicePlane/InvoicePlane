@extends('layouts.master')

@section('content')

    @include('layouts._alerts')

    <section class="content-header">
        <h2 class="mb-4">@lang('ip.dashboard')</h2>
    </section>

    <div class="row dashboard-widgets">
        @foreach ($widgets as $widget)
            @if (config('ip.widgetEnabled' . $widget))
                <div class="dashboard-widget col-12 col-md-{{ config('ip.widgetColumnWidth' . $widget, 6) }}">
                    @include($widget . 'Widget')
                </div>
            @endif
        @endforeach
    </div>

@stop