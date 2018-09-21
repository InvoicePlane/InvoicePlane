@include('layouts._typeahead')

@include('clients._js_lookup')
@include('clients._js_subchange')

<div id="modal-lookup-client" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('ip.change_client')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <div class="form-group row">
                    <label class="col-12 col-sm-3 col-form-label">@lang('ip.client')</label>

                    <div class="col-12 col-sm-9">
                        {!! Form::text('client_name', null, [
                            'id' => 'change_client_name',
                            'class' => 'form-control client-lookup',
                            'autocomplete' => 'off',
                        ]) !!}
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    @lang('ip.cancel')
                </button>
                <button type="button" id="btn-submit-change-client" class="btn btn-success">
                    <i class="fa fa-save fa-mr"></i> @lang('ip.save')
                </button>
            </div>
        </div>
    </div>
</div>
