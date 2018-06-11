@extends('layouts.master')

@section('content')

    <section class="content-header">
        <h1 class="pull-left">
            {{ trans('ip.custom_fields') }}
        </h1>

        <div class="pull-right">
            <a href="{{ route('customFields.create') }}" class="btn btn-primary"><i
                        class="fa fa-plus"></i> {{ trans('ip.new') }}</a>
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
                                <th>{!! Sortable::link('tbl_name', trans('ip.table_name')) !!}</th>
                                <th>{!! Sortable::link('column_name', trans('ip.column_name')) !!}</th>
                                <th>{!! Sortable::link('field_label', trans('ip.field_label')) !!}</th>
                                <th>{!! Sortable::link('field_type', trans('ip.field_type')) !!}</th>
                                <th>{{ trans('ip.options') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($customFields as $customField)
                                <tr>
                                    <td>{{ $tableNames[$customField->tbl_name] }}</td>
                                    <td>{{ $customField->column_name }}</td>
                                    <td>{{ $customField->field_label }}</td>
                                    <td>{{ $customField->field_type }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                    data-toggle="dropdown">
                                                {{ trans('ip.options') }} <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{ route('customFields.edit', [$customField->id]) }}"><i
                                                                class="fa fa-edit"></i> {{ trans('ip.edit') }}</a></li>
                                                <li><a href="{{ route('customFields.delete', [$customField->id]) }}"
                                                       onclick="return confirm('{{ trans('ip.delete_record_warning') }}');"><i
                                                                class="fa fa-trash-o"></i> {{ trans('ip.delete') }}</a>
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
                    {!! $customFields->appends(request()->except('page'))->render() !!}
                </div>

            </div>

        </div>

    </section>

@stop