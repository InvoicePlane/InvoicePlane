@extends('layouts.master')

@section('javascript')

    @include('layouts._datepicker')
    @include('payments._js_form')

@stop

@section('content')

    @if ($editMode == true)
        {!! Form::model($payment, ['route' => ['payments.update', $payment->id]]) !!}
    @else
        {!! Form::open(['route' => 'payments.store']) !!}
    @endif

    {!! Form::hidden('invoice_id') !!}

    <section class="content-header">
        <h1 class="pull-left">
            {{ trans('ip.payment_form') }}
        </h1>

        <div class="pull-right">
            <a href="{{ route('payments.index') }}" class="btn btn-default">Cancel</a>
            {!! Form::submit(trans('ip.save'), ['class' => 'btn btn-primary']) !!}
        </div>
        <div class="clearfix"></div>
    </section>

    <section class="content">

        @include('layouts._alerts')

        <div class="row">

            <div class="col-md-12">

                <div class="box box-primary">

                    <div class="box-body">

                        <div class="form-group">
                            <label>{{ trans('ip.amount') }}: </label>
                            {!! Form::text('amount', $payment->formatted_numeric_amount, ['id' => 'amount',
                            'class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('ip.payment_date') }}: </label>
                            {!! Form::text('paid_at', $payment->formatted_paid_at, ['id' => 'paid_at', 'class'
                            => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('ip.payment_method') }}</label>
                            {!! Form::select('payment_method_id', $paymentMethods, null, ['id' =>
                            'payment_method_id', 'class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('ip.note') }}</label>
                            {!! Form::textarea('note', null, ['id' => 'note', 'class' => 'form-control']) !!}
                        </div>

                        @if ($customFields->count())
                            @include('custom_fields._custom_fields')
                        @endif

                    </div>

                </div>

            </div>

        </div>

    </section>

    {!! Form::close() !!}

    <section class="content">
        @include('notes._notes', ['object' => $payment, 'model' => 'FI\Modules\Payments\Models\Payment', 'showPrivateCheckbox' => true])
    </section>
@stop