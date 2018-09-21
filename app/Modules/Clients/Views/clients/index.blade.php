@extends('layouts.master')

@section('javascript')
    <script>
        $(function () {
            $('#btn-bulk-delete').click(function () {

                var ids = [];

                $('.bulk-record:checked').each(function () {
                    ids.push($(this).data('id'));
                });

                if (ids.length > 0) {
                    if (!confirm('{!! trans('ip.delete_client_warning') !!}')) return false;
                    $.post("{{ route('clients.bulk.delete') }}", {
                        ids: ids
                    }).done(function () {
                        window.location = decodeURIComponent("{{ urlencode(request()->fullUrl()) }}");
                    });
                }
            });
        });
    </script>
@stop

@section('content-header')

    <h5>@lang('ip.clients')</h5>

    <div>

        <a href="javascript:void(0)" class="btn btn-sm btn-outline-danger bulk-actions" id="btn-bulk-delete">
            <i class="fa fa-trash fa-mr"></i> @lang('ip.delete')
        </a>

        <div class="btn-group btn-group-sm mx-3">
            <a href="{{ route('clients.index', ['status' => 'active']) }}" class="btn btn-default
                @if ($status === 'active') active @endif">
                @lang('ip.active')
            </a>
            <a href="{{ route('clients.index', ['status' => 'inactive']) }}" class="btn btn-default
                @if ($status === 'inactive') active @endif">
                @lang('ip.inactive')
            </a>
            <a href="{{ route('clients.index') }}" class="btn btn-default
                @if ($status === 'all') active @endif">
                @lang('ip.all')
            </a>
        </div>

        <a href="{{ route('clients.create') }}" class="btn btn-sm btn-primary">
            <i class="fa fa-plus fa-mr"></i> @lang('ip.new')
        </a>

    </div>

@endsection

@section('content')

    <div class="table-content">

        <div class="table-responsive">
            <table class="table table-hover">

                <thead>
                <tr>
                    <th>
                        <div class="btn-group"><input type="checkbox" id="bulk-select-all"></div>
                    </th>
                    <th>{!! Sortable::link('unique_name', trans('ip.client_name')) !!}</th>
                    <th>{!! Sortable::link('email', trans('ip.email_address')) !!}</th>
                    <th>{!! Sortable::link('phone', trans('ip.phone_number')) !!}</th>
                    <th style="text-align: right;">{!! Sortable::link('balance', trans('ip.balance')) !!}</th>
                    <th>{!! Sortable::link('active', trans('ip.active')) !!}</th>
                    <th>@lang('ip.options')</th>
                </tr>
                </thead>

                <tbody>
                @foreach ($clients as $client)
                    <tr>
                        <td><input type="checkbox" class="bulk-record" data-id="{{ $client->id }}"></td>
                        <td>
                            <a href="{{ route('clients.show', [$client->id]) }}">{{ $client->unique_name }}</a>
                        </td>
                        <td>{{ $client->email }}</td>
                        <td>{{ $client->phone ?: ($client->mobile ?: '') }}</td>
                        <td style="text-align: right;">{{ $client->formatted_balance }}</td>
                        <td>{{ ($client->active) ? trans('ip.yes') : trans('ip.no') }}</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm dropdown-toggle"
                                    data-toggle="dropdown">
                                    @lang('ip.options') <span class="caret"></span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="{{ route('clients.show', [$client->id]) }}" class="dropdown-item"
                                        id="view-client-{{ $client->id }}">
                                        <i class="fa fa-search"></i> @lang('ip.view')
                                    </a>
                                    <a href="{{ route('clients.edit', [$client->id]) }}" class="dropdown-item"
                                        id="edit-client-{{ $client->id }}">
                                        <i class="fa fa-edit"></i> @lang('ip.edit')
                                    </a>
                                    <a href="javascript:void(0)" class="create-quote dropdown-item"
                                        data-unique-name="{{ $client->unique_name }}">
                                        <i class="fa fa-file-text-o"></i> @lang('ip.create_quote')
                                    </a>
                                    <a href="javascript:void(0)" class="create-invoice dropdown-item"
                                        data-unique-name="{{ $client->unique_name }}">
                                        <i class="fa fa-file-text"></i> @lang('ip.create_invoice')
                                    </a>
                                    <a href="{{ route('clients.delete', [$client->id]) }}"
                                        id="delete-client-{{ $client->id }}" class="dropdown-item"
                                        onclick="return confirm('@lang('ip.delete_client_warning')');">
                                        <i class="fa fa-trash-o"></i> @lang('ip.delete')
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>

        {!! $clients->appends(request()->except('page'))->render() !!}

    </div>

@stop
