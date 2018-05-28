@extends('layouts.master')

@section('javascript')
    <script type="text/javascript">
        $(function() {
            $('.btn-show-content').click(function() {
                $('#modal-placeholder').load('{{ route('mailLog.content') }}', {
                    id: $(this).data('id')
                });
            });
        });
    </script>
@stop

@section('content')

    <section class="content-header">
        <h1>{{ trans('fi.mail_log') }}</h1>
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
                                <th>{!! Sortable::link('created_at', trans('fi.date')) !!}</th>
                                <th>{!! Sortable::link('from', trans('fi.from')) !!}</th>
                                <th>{!! Sortable::link('to', trans('fi.to')) !!}</th>
                                <th>{!! Sortable::link('cc', trans('fi.cc')) !!}</th>
                                <th>{!! Sortable::link('bcc', trans('fi.bcc')) !!}</th>
                                <th>{!! Sortable::link('subject', trans('fi.subject')) !!}</th>
                                <th>{!! Sortable::link('sent', trans('fi.sent')) !!}</th>
                                <th>{{ trans('fi.options') }}</th>
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
                                    <td><a href="javascript:void(0)" class="btn-show-content" data-id="{{ $mail->id }}">{{ $mail->subject }}</a></td>
                                    <td>{{ $mail->formatted_sent }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                                {{ trans('fi.options') }} <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{ route('mailLog.delete', [$mail->id]) }}" onclick="return confirm('{{ trans('fi.delete_record_warning') }}');"><i class="fa fa-trash-o"></i> {{ trans('fi.delete') }}</a></li>
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