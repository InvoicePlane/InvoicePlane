<link rel="stylesheet" href="{{ asset('assets/plugins/chosen/chosen.min.css') }}">
<script src="{{ asset('assets/plugins/chosen/chosen.jquery.min.js') }}" type="text/javascript"></script>

@include('quotes._js_mail')

<div class="modal fade" id="modal-mail-quote">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">{{ trans('fi.email_quote') }}</h4>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <form class="form-horizontal">

                    <div class="form-group">
                        <label class="col-sm-3 control-label">{{ trans('fi.to') }}</label>

                        <div class="col-sm-9">
                            {!! $contactDropdownTo !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">{{ trans('fi.cc') }}</label>

                        <div class="col-sm-9">
                            {!! $contactDropdownCc !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">{{ trans('fi.bcc') }}</label>

                        <div class="col-sm-9">
                            {!! $contactDropdownBcc !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">{{ trans('fi.subject') }}</label>

                        <div class="col-sm-9">
                            {!! Form::text('subject', $subject, ['id' => 'subject', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">{{ trans('fi.body') }}</label>

                        <div class="col-sm-9">
                            {!! Form::textarea('body', $body, ['id' => 'body', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">{{ trans('fi.attach_pdf') }}</label>

                        <div class="col-sm-9">
                            {!! Form::checkbox('attach_pdf', 1, config('fi.attachPdf'), ['id' => 'attach_pdf']) !!}
                        </div>
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                <button type="button" id="btn-submit-mail-quote" class="btn btn-primary" data-loading-text="{{ trans('fi.sending') }}...">{{ trans('fi.send') }}</button>
            </div>
        </div>
    </div>
</div>