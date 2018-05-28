@extends('layouts.master')

@section('javascript')
    <script type="text/javascript">
        $(function() {
            $('#btn-bulk-delete').click(function () {

                var ids = [];

                $('.bulk-record:checked').each(function () {
                    ids.push($(this).data('id'));
                });

                if (ids.length > 0) {
                    if (!confirm('{!! trans('fi.delete_client_warning') !!}')) return false;
                    $.post("{{ route('clients.bulk.delete') }}", {
                        ids: ids
                    }).done(function() {
                        window.location = decodeURIComponent("{{ urlencode(request()->fullUrl()) }}");
                    });
                }
            });
        });
    </script>
@stop

@section('content')

    <section class="content-header">
        <h1 class="pull-left">{{ trans('fi.clients') }}</h1>

        <div class="pull-right">

            <a href="javascript:void(0)" class="btn btn-default bulk-actions" id="btn-bulk-delete"><i class="fa fa-trash"></i> {{ trans('fi.delete') }}</a>

            <div class="btn-group">
                <a href="{{ route('clients.index', ['status' => 'active']) }}" class="btn btn-default @if ($status == 'active') active @endif">{{ trans('fi.active') }}</a>
                <a href="{{ route('clients.index', ['status' => 'inactive']) }}" class="btn btn-default @if ($status == 'inactive') active @endif">{{ trans('fi.inactive') }}</a>
                <a href="{{ route('clients.index') }}" class="btn btn-default @if ($status == 'all') active @endif">{{ trans('fi.all') }}</a>
            </div>

            <a href="{{ route('clients.create') }}" class="btn btn-primary btn-margin-left"><i class="fa fa-plus"></i> {{ trans('fi.new') }}</a>
        </div>

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
                                <th><div class="btn-group"><input type="checkbox" id="bulk-select-all"></div></th>
                                <th>{!! Sortable::link('unique_name', trans('fi.client_name')) !!}</th>
                                <th>{!! Sortable::link('email', trans('fi.email_address')) !!}</th>
                                <th>{!! Sortable::link('phone', trans('fi.phone_number')) !!}</th>
                                <th style="text-align: right;">{!! Sortable::link('balance', trans('fi.balance')) !!}</th>
                                <th>{!! Sortable::link('active', trans('fi.active')) !!}</th>
                                <th>{{ trans('fi.options') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($clients as $client)
                                <tr>
                                    <td><input type="checkbox" class="bulk-record" data-id="{{ $client->id }}"></td>
                                    <td><a href="{{ route('clients.show', [$client->id]) }}">{{ $client->unique_name }}</a></td>
                                    <td>{{ $client->email }}</td>
                                    <td>{{ (($client->phone ? $client->phone : ($client->mobile ? $client->mobile : ''))) }}</td>
                                    <td style="text-align: right;">{{ $client->formatted_balance }}</td>
                                    <td>{{ ($client->active) ? trans('fi.yes') : trans('fi.no') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                                {{ trans('fi.options') }} <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{ route('clients.show', [$client->id]) }}" id="view-client-{{ $client->id }}"><i class="fa fa-search"></i> {{ trans('fi.view') }}</a></li>
                                                <li><a href="{{ route('clients.edit', [$client->id]) }}" id="edit-client-{{ $client->id }}"><i class="fa fa-edit"></i> {{ trans('fi.edit') }}</a></li>
                                                <li><a href="javascript:void(0)" class="create-quote" data-unique-name="{{ $client->unique_name }}"><i class="fa fa-file-text-o"></i> {{ trans('fi.create_quote') }}</a></li>
                                                <li><a href="javascript:void(0)" class="create-invoice" data-unique-name="{{ $client->unique_name }}"><i class="fa fa-file-text"></i> {{ trans('fi.create_invoice') }}</a></li>
                                                <li><a href="{{ route('clients.delete', [$client->id]) }}" id="delete-client-{{ $client->id }}" onclick="return confirm('{{ trans('fi.delete_client_warning') }}');"><i class="fa fa-trash-o"></i> {{ trans('fi.delete') }}</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>

                </div>

                <div class="pull-right">
                    {!! $clients->appends(request()->except('page'))->render() !!}
                </div>

            </div>

        </div>

    </section>

@stop