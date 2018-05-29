@include('layouts._datepicker')
@include('payments._js_create')

<div class="modal fade" id="modal-enter-payment">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">{{ trans('fi.enter_payment') }}: {{ trans('fi.invoice') }}
                    #{{ $invoiceNumber }}</h4>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <form class="form-horizontal">

                    <input type="hidden" name="invoice_id" id="invoice_id" value="{{ $invoice_id }}">

                    <div class="form-group">
                        <label class="col-sm-4 control-label">{{ trans('fi.amount') }}</label>

                        <div class="col-sm-8">
                            {!! Form::text('payment_amount', $balance, ['id' => 'payment_amount', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label">{{ trans('fi.payment_date') }}</label>

                        <div class="col-sm-8">
                            {!! Form::text('payment_date', $date, ['id' => 'payment_date', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label">{{ trans('fi.payment_method') }}</label>

                        <div class="col-sm-8">
                            {!! Form::select('payment_method_id', $paymentMethods, null, ['id' => 'payment_method_id', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label">{{ trans('fi.note') }}</label>

                        <div class="col-sm-8">
                            {!! Form::textarea('payment_note', null, ['id' => 'payment_note', 'class' => 'form-control', 'rows' => 4]) !!}
                        </div>
                    </div>

                    @if (config('fi.mailConfigured') and $client->email)
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ trans('fi.email_payment_receipt') }}</label>

                            <div class="col-sm-8">
                                {!! Form::checkbox('email_payment_receipt', 1, config('fi.automaticEmailPaymentReceipts'), ['id' => 'email_payment_receipt']) !!}
                            </div>
                        </div>
                    @endif

                    <div id="payment-custom-fields">
                        @if ($customFields->count())
                            @include('custom_fields._custom_fields_modal')
                        @endif
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                <button type="button" id="enter-payment-confirm" class="btn btn-primary"
                        data-loading-text="{{ trans('fi.please_wait') }}...">{{ trans('fi.submit') }}</button>
            </div>
        </div>
    </div>
</div>