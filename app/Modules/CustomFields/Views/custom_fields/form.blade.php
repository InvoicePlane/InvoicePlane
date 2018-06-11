@extends('layouts.master')

@section('content')

    <script type="text/javascript">
      $(function () {
        $('#name').focus();
      });
    </script>

    @if ($editMode == true)
        {!! Form::model($customField, ['route' => ['customFields.update', $customField->id]]) !!}
    @else
        {!! Form::open(['route' => 'customFields.store']) !!}
    @endif

    <section class="content-header">
        <h1 class="pull-left">
            {{ trans('ip.custom_field_form') }}
        </h1>
        <div class="pull-right">
            <button class="btn btn-primary"><i class="fa fa-save"></i> {{ trans('ip.save') }}</button>
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
                            <label>{{ trans('ip.table_name') }}: </label>
                            @if ($editMode == true)
                                {!! Form::text('tbl_name', $tableNames[$customField->tbl_name], ['id' => 'tbl_name', 'readonly' => 'readonly', 'class' => 'form-control']) !!}
                            @else
                                {!! Form::select('tbl_name', $tableNames, null, ['id' => 'tbl_name', 'class' => 'form-control']) !!}
                            @endif
                        </div>

                        <div class="form-group">
                            <label>{{ trans('ip.field_label') }}: </label>
                            {!! Form::text('field_label', null, ['id' => 'field_label', 'class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('ip.field_type') }}: </label>
                            {!! Form::select('field_type', $fieldTypes, null, ['id' => 'field_type', 'class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('ip.field_meta') }}: </label>
                            {!! Form::text('field_meta', null, ['id' => 'field_meta', 'class' => 'form-control']) !!}
                            <span class="help-block">{{ trans('ip.field_meta_description') }}</span>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    {!! Form::close() !!}
@stop