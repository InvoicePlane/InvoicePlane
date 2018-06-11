@include('clients._js_subedit')

{!! Form::model($client, ['route' => ['clients.ajax.modalUpdate', $client->id], 'id' => 'form-edit-client']) !!}
<div class="modal" id="modal-edit-client">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">@lang('ip.edit_client')</h4>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                @include('clients._form')

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('ip.cancel')</button>
                <input type="submit" id="btn-edit-client-submit" class="btn btn-primary" value="@lang('ip.save')">
            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}