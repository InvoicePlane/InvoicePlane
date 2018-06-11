@extends('layouts.master')

@section('content')

    <section class="content-header">
        <h1 class="pull-left">
            @lang('ip.tax_rates')
        </h1>

        <div class="pull-right">
            <a href="{{ route('taxRates.create') }}" class="btn btn-primary"><i
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
                                <th>{!! Sortable::link('percent', trans('ip.percent')) !!}</th>
                                <th>{!! Sortable::link('is_compound', trans('ip.compound')) !!}</th>
                                <th>@lang('ip.options')</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($taxRates as $taxRate)
                                <tr>
                                    <td>{{ $taxRate->name }}</td>
                                    <td>{{ $taxRate->formatted_percent }}</td>
                                    <td>{{ $taxRate->formatted_is_compound }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                    data-toggle="dropdown">
                                                @lang('ip.options') <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{ route('taxRates.edit', [$taxRate->id]) }}"><i
                                                                class="fa fa-edit"></i> @lang('ip.edit')</a></li>
                                                <li><a href="{{ route('taxRates.delete', [$taxRate->id]) }}"
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
                    {!! $taxRates->appends(request()->except('page'))->render() !!}
                </div>

            </div>

        </div>

    </section>

@stop