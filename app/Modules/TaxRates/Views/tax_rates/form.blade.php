@extends('layouts.master')

@section('content')

    <script>
        $(function () {
            $('#name').focus();
        });
    </script>

    @if ($editMode == true)
        {!! Form::model($taxRate, ['route' => ['taxRates.update', $taxRate->id]]) !!}
    @else
        {!! Form::open(['route' => 'taxRates.store']) !!}
    @endif

    <section class="content-header">
        <h1 class="pull-left">
            @lang('ip.tax_rate_form')
        </h1>
        <div class="pull-right">
            <button class="btn btn-primary"><i class="fa fa-save"></i> @lang('ip.save')</button>
        </div>
        <div class="clearfix"></div>
    </section>

    <section class="content">

        @include('layouts._alerts')

        @if ($editMode and $taxRate->in_use)
            <div class="alert alert-warning">@lang('ip.cannot_edit_record_in_use')</div>
        @endif

        <div class="row">

            <div class="col-md-12">

                <div class="box box-primary">

                    <div class="box-body">

                        <div class="form-group">
                            <label>@lang('ip.tax_rate_name'): </label>
                            {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label>@lang('ip.tax_rate_percent'): </label>
                            @if ($editMode and $taxRate->in_use)
                                {!! Form::text('percent', (($editMode) ? $taxRate->formatted_numeric_percent : null),
                                ['id' => 'percent', 'class' => 'form-control', 'readonly' => 'readonly']) !!}
                            @else
                                {!! Form::text('percent', (($editMode) ? $taxRate->formatted_numeric_percent : null),
                                ['id' => 'percent', 'class' => 'form-control']) !!}
                            @endif

                        </div>

                        <div class="form-group">
                            <label>@lang('ip.calculate_as_vat_gst'):</label>
                            @if ($editMode and $taxRate->in_use)
                                {!! Form::select('calculate_vat', ['0' => trans('ip.no'), '1' => trans('ip.yes')],
                                null, ['class' => 'form-control', 'readonly' => 'readonly', 'disabled' =>
                                'disabled']) !!}
                            @else
                                {!! Form::select('calculate_vat', ['0' => trans('ip.no'), '1' => trans('ip.yes')],
                                null, ['class' => 'form-control']) !!}
                            @endif
                        </div>

                        <div class="form-group">
                            <label>@lang('ip.compound'):</label>
                            @if ($editMode and $taxRate->in_use)
                                {!! Form::select('is_compound', ['0' => trans('ip.no'), '1' => trans('ip.yes')],
                                null, ['class' => 'form-control', 'readonly' => 'readonly', 'disabled' =>
                                'disabled']) !!}
                            @else
                                {!! Form::select('is_compound', ['0' => trans('ip.no'), '1' => trans('ip.yes')],
                                null, ['class' => 'form-control']) !!}
                            @endif

                            <span class="help-block">@lang('ip.compound_tax_note')</span>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    {!! Form::close() !!}
@stop
