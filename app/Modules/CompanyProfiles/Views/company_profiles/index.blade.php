@extends('layouts.master')

@section('content')

    <section class="content-header">
        <h1 class="pull-left">
            @lang('ip.company_profiles')
        </h1>

        <div class="pull-right">
            <a href="{{ route('companyProfiles.create') }}" class="btn btn-primary"><i
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
                                <th>@lang('ip.company')</th>
                                <th>@lang('ip.options')</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($companyProfiles as $companyProfile)
                                <tr>
                                    <td>{{ $companyProfile->company }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                data-toggle="dropdown">
                                                @lang('ip.options') <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li>
                                                    <a href="{{ route('companyProfiles.edit', [$companyProfile->id]) }}"><i
                                                            class="fa fa-edit"></i> @lang('ip.edit')</a></li>
                                                <li>
                                                    <a href="{{ route('companyProfiles.delete', [$companyProfile->id]) }}"
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
                    {!! $companyProfiles->appends(request()->except('page'))->render() !!}
                </div>

            </div>

        </div>

    </section>

@stop
