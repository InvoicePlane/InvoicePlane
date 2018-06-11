@extends('layouts.master')

@section('content')

    <script type="text/javascript">
      $(function () {
        $('#name').focus();
      });
    </script>

    @if ($editMode == true)
        {!! Form::model($group, ['route' => ['groups.update', $group->id]]) !!}
    @else
        {!! Form::open(['route' => 'groups.store']) !!}
    @endif

    <section class="content-header">
        <h1 class="pull-left">
            {{ trans('ip.group_form') }}
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
                            <label>{{ trans('ip.name') }}: </label>
                            {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('ip.format') }}: </label>
                            {!! Form::text('format', null, ['id' => 'format', 'class' => 'form-control']) !!}
                            <span class="help-block">{{ trans('ip.available_fields') }}: {NUMBER} {YEAR} {MONTH} {MONTHSHORTNAME} {WEEK}</span>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('ip.next_number') }}: </label>
                            {!! Form::text('next_id', isset($group->next_id) ? $group->next_id : 1, ['id' => 'next_id', 'class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('ip.left_pad') }}: </label>
                            {!! Form::text('left_pad', isset($group->left_pad) ? $group->left_pad : 0, ['id' => 'left_pad', 'class' => 'form-control']) !!}
                            <span class="help-block">{{ trans('ip.left_pad_description') }}</span>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('ip.reset_number') }}: </label>
                            {!! Form::select('reset_number', $resetNumberOptions, null, ['id' => 'reset_number', 'class' => 'form-control']) !!}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    {!! Form::close() !!}
@stop