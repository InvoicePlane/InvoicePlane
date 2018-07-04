@extends('layouts.master')

@section('content')

    @include('layouts._alerts')

    <section class="content-header">
        <h3 class="mb-3">@lang('ip.dashboard')</h3>
    </section>

    <div class="row">
        @foreach ($widgets as $widget)
            @if (config('fi.widgetEnabled' . $widget))
                <div class="col-md-{{ config('fi.widgetColumnWidth' . $widget) }} col-sm-12">
                    @include($widget . 'Widget')
                </div>
            @endif
        @endforeach
    </div>

@stop