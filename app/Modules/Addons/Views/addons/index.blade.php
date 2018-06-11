@extends('layouts.master')

@section('content')

    <section class="content-header">
        <h1>@lang('ip.addons')</h1>
        <div class="clearfix"></div>
    </section>

    <section class="content">

        @include('layouts._alerts')

        <div class="row">

            <div class="col-xs-12">

                <div class="box box-primary">

                    <div class="box-body no-padding">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>@lang('ip.name')</th>
                                <th>@lang('ip.author')</th>
                                <th>@lang('ip.web_address')</th>
                                <th>@lang('ip.status')</th>
                                <th>@lang('ip.options')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($addons as $addon)
                                <tr>
                                    <td>{{ $addon->name }}</td>
                                    <td>{{ $addon->author_name }}</td>
                                    <td>{{ $addon->author_url }}</td>
                                    <td>
                                        @if ($addon->enabled)
                                            <span class="label label-success">@lang('ip.enabled')</span>
                                        @else
                                            <span class="label label-danger">@lang('ip.disabled')</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($addon->enabled)
                                            <a href="{{ route('addons.uninstall', [$addon->id]) }}"
                                               class="btn btn-sm btn-default"
                                               onclick="return confirm('@lang('ip.uninstall_addon_warning')');">@lang('ip.disable')</a>
                                            @if ($addon->has_pending_migrations)
                                                <a href="{{ route('addons.upgrade', [$addon->id]) }}"
                                                   class="btn btn-sm btn-info">@lang('ip.complete_upgrade')</a>
                                            @endif
                                        @else
                                            <a href="{{ route('addons.install', [$addon->id]) }}"
                                               class="btn btn-sm btn-default">@lang('ip.install')</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>

        </div>

    </section>

@stop