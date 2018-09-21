@include('clients._js_subedit')

{!! Form::model($client, ['route' => ['clients.ajax.modalUpdate', $client->id], 'id' => 'form-edit-client']) !!}
<div id="modal-edit-client" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('ip.edit_client')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <div id="modal-status-placeholder"></div>
                @include('clients._form')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    @lang('ip.cancel')
                </button>
                <button type="submit" id="btn-edit-client-submit" class="btn btn-success">
                    <i class="fa fa-save fa-mr"></i> @lang('ip.save')
                </button>
            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}
