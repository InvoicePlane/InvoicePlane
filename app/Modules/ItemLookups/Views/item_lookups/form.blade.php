@extends('layouts.master')

@section('content')

    <script type="text/javascript">
      $(function () {
        $('#name').focus();
      });
    </script>

    @if ($editMode == true)
        {!! Form::model($itemLookup, ['route' => ['itemLookups.update', $itemLookup->id]]) !!}
    @else
        {!! Form::open(['route' => 'itemLookups.store']) !!}
    @endif

    <section class="content-header">
        <h1 class="pull-left">
            {{ trans('ip.item_lookup_form') }}
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
                            <label class="">{{ trans('ip.name') }}: </label>
                            {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label class="">{{ trans('ip.description') }}: </label>
                            {!! Form::textarea('description', null, ['id' => 'description', 'class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label class="">{{ trans('ip.price') }}: </label>
                            {!! Form::text('price', (($editMode) ? $itemLookup->formatted_numeric_price: null), ['id' => 'price', 'class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label class="">{{ trans('ip.tax_1') }}: </label>
                            {!! Form::select('tax_rate_id', $taxRates, null, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label class="">{{ trans('ip.tax_2') }}: </label>
                            {!! Form::select('tax_rate_2_id', $taxRates, null, ['class' => 'form-control']) !!}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    {!! Form::close() !!}
@stop