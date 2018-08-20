@extends('layouts.master')

@section('content-form')

    @if ($editMode)
        {!! Form::model($client, ['route' => ['clients.update', $client->id]]) !!}
    @else
        {!! Form::open(['route' => 'clients.store']) !!}
    @endif

@endsection

@section('content-header')

    <h5>@lang('ip.client_form')</h5>

    <div>
        @if ($editMode)
            <a href="{{ $returnUrl }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-arrow-left fa-mr"></i> @lang('ip.back')
            </a>
        @endif
        <button type="submit" class="btn btn-sm btn-success">
            <i class="fa fa-save fa-mr"></i> @lang('ip.save')
        </button>
    </div>

@endsection

@section('content')

    <ul class="nav nav-tabs" id="client-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="general-tab" data-toggle="tab" href="#tab-general" role="tab"
                aria-controls="general" aria-selected="true">
                @lang('ip.general')
            </a>
        </li>
        @if ($editMode)
            <li class="nav-item">
                <a class="nav-link" id="contacts-tab" data-toggle="tab" href="#tab-contacts" role="tab"
                    aria-controls="notes" aria-selected="true">
                    @lang('ip.contacts')
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="-tab" data-toggle="tab" href="#tab-attachments" role="tab"
                    aria-controls="" aria-selected="false">
                    @lang('ip.attachments')
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="-tab" data-toggle="tab" href="#tab-notes" role="tab"
                    aria-controls="" aria-selected="false">
                    @lang('ip.notes')
                </a>
            </li>
        @endif
    </ul>

    <div class="tab-content" id="client-tab-content">
        <div class="tab-pane fade pt-4 show active" id="tab-general" role="tabpanel" aria-labelledby="general-tab">
            @include('clients._form')
        </div>
        @if ($editMode)
            <div class="tab-pane fade pt-4" id="tab-contacts" role="tabpanel" aria-labelledby="contacts-tab">
                @include('clients._contacts', [
                    'contacts' => $client->contacts()->orderBy('name')->get(),
                    'clientId' => $client->id,
                ])
            </div>
            <div class="tab-pane fade pt-4" id="tab-attachments" role="tabpanel" aria-labelledby="attachments-tab">
                @include('attachments._table', [
                    'object' => $client,
                    'model' => 'IP\Modules\Clients\Models\Client',
                ])
            </div>
            <div class="tab-pane fade pt-4" id="tab-notes" role="tabpanel" aria-labelledby="notes-tab">
                @include('notes._notes', [
                    'object' => $client,
                    'model' => 'IP\Modules\Clients\Models\Client',
                    'hideHeader' => true,
                ])
            </div>
        @endif
    </div>

@stop
