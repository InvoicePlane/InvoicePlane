@extends('layouts.master')

@section('content')

    @include('layouts._alerts')

    <section class="content-header">
        <h1>@lang('ip.dashboard')</h1>
    </section>

    @foreach ($widgets as $widget)
        @if (config('ip.widgetEnabled' . $widget))
            <div class="col-sm-12 col-md-{{ config('ip.widgetColumnWidth' . $widget, 6) }}">
                @include($widget . 'Widget')
            </div>
        @endif
    @endforeach

@stop