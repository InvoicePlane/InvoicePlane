@extends('layouts.master')

@section('head')
    @include('layouts._datepicker')
    @include('layouts._typeahead')
    @include('clients._js_lookup')
    @include('expenses._js_vendor_lookup')
    @include('expenses._js_category_lookup')
@stop

@section('javascript')
    <script type="text/javascript">
      $(function () {
        $('#expense_date').datepicker({format: '{{ config('fi.datepickerFormat') }}', autoclose: true});
      });
    </script>
@stop

@section('content')

    @if ($editMode == true)
        {!! Form::model($expense, ['route' => ['expenses.update', $expense->id], 'files' => true]) !!}
    @else
        {!! Form::open(['route' => 'expenses.store', 'files' => true]) !!}
    @endif

    {!! Form::hidden('user_id', auth()->user()->id) !!}

    <section class="content-header">
        <h1 class="pull-left">
            @lang('ip.expense_form')
        </h1>
        <div class="pull-right">
            <button class="btn btn-primary"><i class="fa fa-save"></i> @lang('ip.save')</button>
        </div>
        <div class="clearfix"></div>
    </section>

    <section class="content">

        @include('layouts._alerts')

        <div class="row">

            <div class="col-md-12">

                <div class="box box-primary">

                    <div class="box-body">

                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>* @lang('ip.company_profile'): </label>
                                    {!! Form::select('company_profile_id', $companyProfiles, (($editMode) ? $expense->company_profile_id : config('fi.defaultCompanyProfile')), ['id' => 'company_profile_id', 'class' => 'form-control']) !!}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>* @lang('ip.date'): </label>
                                    {!! Form::text('expense_date', (($editMode) ? $expense->formatted_expense_date : $currentDate), ['id' => 'expense_date', 'class' => 'form-control']) !!}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>* @lang('ip.category'): </label>
                                    {!! Form::text('category_name', null, ['id' => 'category_name', 'class' => 'form-control category-lookup']) !!}
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>* @lang('ip.amount'): </label>
                                    {!! Form::text('amount', (($editMode) ? $expense->formatted_numeric_amount : null), ['id' => 'amount', 'class' => 'form-control']) !!}
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>@lang('ip.tax'): </label>
                                    {!! Form::text('tax', (($editMode) ? $expense->formatted_numeric_tax : null), ['id' => 'amount', 'class' => 'form-control']) !!}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('ip.vendor'): </label>
                                    {!! Form::text('vendor_name', null, ['id' => 'vendor_name', 'class' => 'form-control vendor-lookup']) !!}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('ip.client'): </label>
                                    {!! Form::text('client_name', null, ['id' => 'client_name', 'class' => 'form-control client-lookup']) !!}
                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <label>@lang('ip.description'): </label>
                            {!! Form::textarea('description', null, ['id' => 'description', 'class' => 'form-control']) !!}
                        </div>

                        @if ($customFields->count())
                            @include('custom_fields._custom_fields')
                        @endif

                        @if (!$editMode)
                            @if (!config('app.demo'))
                                <div class="form-group">
                                    <label>@lang('ip.attach_files'): </label>
                                    {!! Form::file('attachments[]', ['id' => 'attachments', 'class' => 'form-control', 'multiple' => 'multiple']) !!}
                                </div>
                            @endif
                        @else
                            @include('attachments._table', ['object' => $expense, 'model' => 'FI\Modules\Expenses\Models\Expense'])
                        @endif
                    </div>

                </div>

            </div>

        </div>

    </section>

    {!! Form::close() !!}
@stop