@extends('layouts.master')

@section('content')

    <section class="content-header">
        <h1>{{ trans('ip.addons') }}</h1>
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
                                <th>{{ trans('ip.name') }}</th>
                                <th>{{ trans('ip.author') }}</th>
                                <th>{{ trans('ip.web_address') }}</th>
                                <th>{{ trans('ip.status') }}</th>
                                <th>{{ trans('ip.options') }}</th>
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
                                            <span class="label label-success">{{ trans('ip.enabled') }}</span>
                                        @else
                                            <span class="label label-danger">{{ trans('ip.disabled') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($addon->enabled)
                                            <a href="{{ route('addons.uninstall', [$addon->id]) }}"
                                               class="btn btn-sm btn-default"
                                               onclick="return confirm('{{ trans('ip.uninstall_addon_warning') }}');">{{ trans('ip.disable') }}</a>
                                            @if ($addon->has_pending_migrations)
                                                <a href="{{ route('addons.upgrade', [$addon->id]) }}"
                                                   class="btn btn-sm btn-info">{{ trans('ip.complete_upgrade') }}</a>
                                            @endif
                                        @else
                                            <a href="{{ route('addons.install', [$addon->id]) }}"
                                               class="btn btn-sm btn-default">{{ trans('ip.install') }}</a>
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