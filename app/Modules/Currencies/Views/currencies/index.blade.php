@extends('layouts.master')

@section('content')

    <section class="content-header">
        <h1 class="pull-left">
            @lang('ip.currencies')
        </h1>

        <div class="pull-right">
            <a href="{{ route('currencies.create') }}" class="btn btn-primary"><i
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
                                <th>{!! Sortable::link('name', trans('ip.name')) !!}</th>
                                <th>{!! Sortable::link('code', trans('ip.code')) !!}</th>
                                <th>{!! Sortable::link('symbol', trans('ip.symbol')) !!}</th>
                                <th>{!! Sortable::link('placement', trans('ip.symbol_placement')) !!}</th>
                                <th>{!! Sortable::link('decimal', trans('ip.decimal_point')) !!}</th>
                                <th>{!! Sortable::link('thousands', trans('ip.thousands_separator')) !!}</th>
                                <th>@lang('ip.options')</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($currencies as $currency)
                                <tr>
                                    <td>{{ $currency->name }}</td>
                                    <td>{{ $currency->code }}</td>
                                    <td>{{ $currency->symbol }}</td>
                                    <td>{{ $currency->formatted_placement }}</td>
                                    <td>{{ $currency->decimal }}</td>
                                    <td>{{ $currency->thousands }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                    data-toggle="dropdown">
                                                @lang('ip.options') <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{ route('currencies.edit', [$currency->id]) }}"><i
                                                                class="fa fa-edit"></i> @lang('ip.edit')</a></li>
                                                <li><a href="{{ route('currencies.delete', [$currency->id]) }}"
                                                       onclick="return confirm('@lang('ip.delete_record_warning')');"><i
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
                    {!! $currencies->appends(request()->except('page'))->render() !!}
                </div>

            </div>

        </div>

    </section>
@stop