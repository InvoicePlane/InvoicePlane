@extends('layouts.master')

@section('content-header')
    <h5>@lang('ip.addons')</h5>
@endsection

@section('content')

    <div class="table-content">
        <div class="table-responsive">
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
                                <span class="badge badge-success">@lang('ip.enabled')</span>
                            @else
                                <span class="badge badge-danger">@lang('ip.disabled')</span>
                            @endif
                        </td>
                        <td>
                            @if ($addon->enabled)
                                <a href="{{ route('addons.uninstall', [$addon->id]) }}"
                                    class="btn btn-sm btn-outline-warning"
                                    onclick="return confirm('@lang('ip.uninstall_addon_warning')');">
                                    @lang('ip.disable')
                                </a>
                                @if ($addon->has_pending_migrations)
                                    <a href="{{ route('addons.upgrade', [$addon->id]) }}"
                                        class="btn btn-sm btn-warning">
                                        @lang('ip.complete_upgrade')
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('addons.install', [$addon->id]) }}"
                                    class="btn btn-sm btn-primary">
                                    @lang('ip.install')
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@stop
