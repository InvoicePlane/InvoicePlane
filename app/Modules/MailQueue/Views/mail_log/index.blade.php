@extends('layouts.master')

@section('javascript')
    <script>
        $(function () {
            $('.btn-show-content').click(function () {
                $('#modal-placeholder').load('{{ route('mailLog.content') }}', {
                    id: $(this).data('id')
                });
            });
        });
    </script>
@stop

@section('content')

    <section class="content-header">
        <h1>@lang('ip.mail_log')</h1>
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
                                <th>{!! Sortable::link('created_at', trans('ip.date')) !!}</th>
                                <th>{!! Sortable::link('from', trans('ip.from')) !!}</th>
                                <th>{!! Sortable::link('to', trans('ip.to')) !!}</th>
                                <th>{!! Sortable::link('cc', trans('ip.cc')) !!}</th>
                                <th>{!! Sortable::link('bcc', trans('ip.bcc')) !!}</th>
                                <th>{!! Sortable::link('subject', trans('ip.subject')) !!}</th>
                                <th>{!! Sortable::link('sent', trans('ip.sent')) !!}</th>
                                <th>@lang('ip.options')</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($mails as $mail)
                                <tr>
                                    <td>{{ $mail->formatted_created_at }}</td>
                                    <td>{{ $mail->formatted_from }}</td>
                                    <td>{{ $mail->formatted_to }}</td>
                                    <td>{{ $mail->formatted_cc }}</td>
                                    <td>{{ $mail->formatted_bcc }}</td>
                                    <td><a href="javascript:void(0)" class="btn-show-content"
                                            data-id="{{ $mail->id }}">{{ $mail->subject }}</a></td>
                                    <td>{{ $mail->formatted_sent }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                data-toggle="dropdown">
                                                @lang('ip.options') <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{ route('mailLog.delete', [$mail->id]) }}"
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
                    {!! $mails->appends(request()->except('page'))->render() !!}
                </div>

            </div>

        </div>

    </section>
@stop
