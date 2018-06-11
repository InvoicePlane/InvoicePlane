@include('invoices._js_copy')

<div class="modal fade" id="modal-copy-invoice">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">@lang('ip.copy')</h4>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <form class="form-horizontal">

                    <div class="form-group">
                        <label class="col-sm-3 control-label">@lang('ip.client')</label>
                        <div class="col-sm-9">
                            {!! Form::text('client_name', $invoice->client->unique_name, ['id' => 'copy_client_name', 'class' => 'form-control client-lookup', 'autocomplete' => 'off']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">@lang('ip.date')</label>
                        <div class="col-sm-9">
                            {!! Form::text('invoice_date', date(config('fi.dateFormat')), ['id' => 'copy_invoice_date', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">@lang('ip.company_profile')</label>
                        <div class="col-sm-9">
                            {!! Form::select('company_profile_id', $companyProfiles, config('fi.defaultCompanyProfile'),
                            ['id' => 'copy_company_profile_id', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">@lang('ip.group')</label>
                        <div class="col-sm-9">
                            {!! Form::select('group_id', $groups, $invoice->group_id, ['id' => 'copy_group_id', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('ip.cancel')</button>
                <button type="button" id="btn-copy-invoice-submit"
                        class="btn btn-primary">@lang('ip.submit')</button>
            </div>
        </div>
    </div>
</div>