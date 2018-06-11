@extends('layouts.master')

@section('content')

    <section class="content-header">
        <h1 class="pull-left">
            {{ trans('ip.groups') }}
        </h1>
        <div class="pull-right">
            <a href="{{ route('groups.create') }}" class="btn btn-primary"><i
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
                                <th>{!! Sortable::link('name', trans('ip.name')) !!}</th>
                                <th>{!! Sortable::link('format', trans('ip.format')) !!}</th>
                                <th>{!! Sortable::link('next_id', trans('ip.next_number')) !!}</th>
                                <th>{!! Sortable::link('left_pad', trans('ip.left_pad')) !!}</th>
                                <th>{!! Sortable::link('reset_number', trans('ip.reset_number')) !!}</th>
                                <th>{{ trans('ip.options') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($groups as $group)
                                <tr>
                                    <td>{{ $group->name }}</td>
                                    <td>{{ $group->format }}</td>
                                    <td>{{ $group->next_id }}</td>
                                    <td>{{ $group->left_pad }}</td>
                                    <td>{{ $resetNumberOptions[$group->reset_number] }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                    data-toggle="dropdown">
                                                {{ trans('ip.options') }} <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{ route('groups.edit', [$group->id]) }}"><i
                                                                class="fa fa-edit"></i> {{ trans('ip.edit') }}</a></li>
                                                <li><a href="{{ route('groups.delete', [$group->id]) }}"
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
                    {!! $groups->appends(request()->except('page'))->render() !!}
                </div>

            </div>

        </div>

    </section>

@stop