<link rel="stylesheet" href="{{ asset('assets/dist/chosen-js/chosen.css') }}">
<script src="{{ asset('assets/dist/chosen-js/chosen.jquery.js') }}"></script>

@include('quotes._js_mail')

<div class="modal fade" id="modal-mail-quote">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">@lang('ip.email_quote')</h4>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <form class="form-horizontal">

                    <div class="form-group">
                        <label class="col-sm-3 control-label">@lang('ip.to')</label>

                        <div class="col-sm-9">
                            {!! $contactDropdownTo !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">@lang('ip.cc')</label>

                        <div class="col-sm-9">
                            {!! $contactDropdownCc !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">@lang('ip.bcc')</label>

                        <div class="col-sm-9">
                            {!! $contactDropdownBcc !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">@lang('ip.subject')</label>

                        <div class="col-sm-9">
                            {!! Form::text('subject', $subject, ['id' => 'subject', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">@lang('ip.body')</label>

                        <div class="col-sm-9">
                            {!! Form::textarea('body', $body, ['id' => 'body', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">@lang('ip.attach_pdf')</label>

                        <div class="col-sm-9">
                            {!! Form::checkbox('attach_pdf', 1, config('fi.attachPdf'), ['id' => 'attach_pdf']) !!}
                        </div>
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('ip.cancel')</button>
                <button type="button" id="btn-submit-mail-quote" class="btn btn-primary"
                        data-loading-text="@lang('ip.sending')...">@lang('ip.send')</button>
            </div>
        </div>
    </div>
</div>