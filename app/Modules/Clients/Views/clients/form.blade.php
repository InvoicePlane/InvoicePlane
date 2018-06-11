@extends('layouts.master')

@section('content')

    @if ($editMode)
        {!! Form::model($client, ['route' => ['clients.update', $client->id]]) !!}
    @else
        {!! Form::open(['route' => 'clients.store']) !!}
    @endif

    <section class="content-header">
        <h1 class="pull-left">@lang('ip.client_form')</h1>

        <div class="pull-right">
            @if ($editMode)
                <a href="{{ $returnUrl }}" class="btn btn-default"><i class="fa fa-backward"></i> @lang('ip.back')
                </a>
            @endif
            <button class="btn btn-primary"><i class="fa fa-save"></i> @lang('ip.save')</button>
        </div>

        <div class="clearfix"></div>
    </section>

    <section class="content">

        @include('layouts._alerts')

        <div class="row">

            <div class="col-md-12">

                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab-general" data-toggle="tab">@lang('ip.general')</a></li>
                        @if ($editMode)
                            <li><a href="#tab-contacts" data-toggle="tab">@lang('ip.contacts')</a></li>
                            <li><a href="#tab-attachments" data-toggle="tab">@lang('ip.attachments')</a></li>
                            <li><a href="#tab-notes" data-toggle="tab">@lang('ip.notes')</a></li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-general">
                            @include('clients._form')
                        </div>
                        @if ($editMode)
                            <div class="tab-pane" id="tab-contacts">
                                @include('clients._contacts', ['contacts' => $client->contacts()->orderBy('name')->get(), 'clientId' => $client->id])
                            </div>
                            <div class="tab-pane" id="tab-attachments">
                                @include('attachments._table', ['object' => $client, 'model' => 'FI\Modules\Clients\Models\Client'])
                            </div>
                            <div class="tab-pane" id="tab-notes">
                                @include('notes._notes', ['object' => $client, 'model' => 'FI\Modules\Clients\Models\Client', 'hideHeader' => true])
                            </div>
                        @endif
                    </div>
                </div>

            </div>

        </div>

    </section>

    {!! Form::close() !!}

@stop