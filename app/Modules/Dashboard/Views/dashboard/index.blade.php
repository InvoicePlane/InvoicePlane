@extends('layouts.master')

@section('content')

    @include('layouts._alerts')

    <section class="content-header">
        <h1>{{ trans('ip.dashboard') }}</h1>
    </section>

    @foreach ($widgets as $widget)
        @if (config('fi.widgetEnabled' . $widget))
            <div class="col-md-{{ config('fi.widgetColumnWidth' . $widget) }} col-sm-12">
                @include($widget . 'Widget')
            </div>
        @endif
    @endforeach

@stop