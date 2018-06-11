@include('layouts._typeahead')

@include('clients._js_lookup')
@include('clients._js_subchange')

<div class="modal fade" id="modal-lookup-client">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">{{ trans('ip.change_client') }}</h4>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <form class="form-horizontal">

                    <div class="form-group">
                        <label class="col-sm-3 control-label">{{ trans('ip.client') }}</label>

                        <div class="col-sm-9">
                            {!! Form::text('client_name', null, ['id' => 'change_client_name', 'class' =>
                            'form-control client-lookup', 'autocomplete' => 'off']) !!}
                        </div>
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('ip.cancel') }}</button>
                <button type="button" id="btn-submit-change-client" class="btn btn-primary">{{ trans('ip.save') }}
                </button>
            </div>
        </div>
    </div>
</div>