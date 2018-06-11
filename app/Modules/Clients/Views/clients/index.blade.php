@extends('layouts.master')

@section('javascript')
    <script type="text/javascript">
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

@section('content')

    <section class="content-header">
        <h1 class="pull-left">@lang('ip.clients')</h1>

        <div class="pull-right">

            <a href="javascript:void(0)" class="btn btn-default bulk-actions" id="btn-bulk-delete"><i
                        class="fa fa-trash"></i> @lang('ip.delete')</a>

            <div class="btn-group">
                <a href="{{ route('clients.index', ['status' => 'active']) }}"
                   class="btn btn-default @if ($status == 'active') active @endif">@lang('ip.active')</a>
                <a href="{{ route('clients.index', ['status' => 'inactive']) }}"
                   class="btn btn-default @if ($status == 'inactive') active @endif">@lang('ip.inactive')</a>
                <a href="{{ route('clients.index') }}"
                   class="btn btn-default @if ($status == 'all') active @endif">@lang('ip.all')</a>
            </div>

            <a href="{{ route('clients.create') }}" class="btn btn-primary btn-margin-left"><i
                        class="fa fa-plus"></i> @lang('ip.new')</a>
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
                                    <td>{{ (($client->phone ? $client->phone : ($client->mobile ? $client->mobile : ''))) }}</td>
                                    <td style="text-align: right;">{{ $client->formatted_balance }}</td>
                                    <td>{{ ($client->active) ? trans('ip.yes') : trans('ip.no') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                    data-toggle="dropdown">
                                                @lang('ip.options') <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{ route('clients.show', [$client->id]) }}"
                                                       id="view-client-{{ $client->id }}"><i
                                                                class="fa fa-search"></i> @lang('ip.view')</a>
                                                </li>
                                                <li><a href="{{ route('clients.edit', [$client->id]) }}"
                                                       id="edit-client-{{ $client->id }}"><i
                                                                class="fa fa-edit"></i> @lang('ip.edit')</a></li>
                                                <li><a href="javascript:void(0)" class="create-quote"
                                                       data-unique-name="{{ $client->unique_name }}"><i
                                                                class="fa fa-file-text-o"></i> @lang('ip.create_quote')
                                                    </a></li>
                                                <li><a href="javascript:void(0)" class="create-invoice"
                                                       data-unique-name="{{ $client->unique_name }}"><i
                                                                class="fa fa-file-text"></i> @lang('ip.create_invoice')
                                                    </a></li>
                                                <li><a href="{{ route('clients.delete', [$client->id]) }}"
                                                       id="delete-client-{{ $client->id }}"
                                                       onclick="return confirm('@lang('ip.delete_client_warning')');"><i
                                                                class="fa fa-trash-o"></i> @lang('ip.delete')</a>
                                                </li>
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