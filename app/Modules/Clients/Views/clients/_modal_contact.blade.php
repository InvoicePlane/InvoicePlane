<script>
    $(function () {
        var modalContact = $('#modal-contact');

        modalContact.modal();

        $('#btn-contact-submit').click(function () {
            $.post("{{ $submitRoute }}", {
                client_id: '{{ $clientId }}',
                name: $('#contact_name').val(),
                email: $('#contact_email').val()
            }).fail(function (response) {
                showErrors($.parseJSON(response.responseText).errors, '#modal-status-placeholder');
            }).done(function (response) {
                modalContact.modal('hide');
                $('#tab-contacts').html(response);
            });
        });
    });
</script>

<div id="modal-contact" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    @if ($editMode)
                        @lang('ip.edit_contact')
                    @else
                        @lang('ip.add_contact')
                    @endif
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <div class="form-group row">
                    <label class="col-12 col-sm-3 col-form-label">@lang('ip.name')</label>
                    <div class="col-12 col-sm-9">
                        {!! Form::text('contact_name', ($editMode) ? $contact->name : null, [
                            'class' => 'form-control',
                            'id' => 'contact_name',
                        ]) !!}
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-12 col-sm-3 col-form-label">@lang('ip.email'):</label>
                    <div class="col-12 col-sm-9">
                        {!! Form::email('contact_email', ($editMode) ? $contact->email : null, [
                            'class' => 'form-control',
                            'id' => 'contact_email',
                        ]) !!}
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">@lang('ip.cancel')</button>
                <button type="button" id="btn-contact-submit" class="btn btn-success">
                    <i class="fa fa-save fa-mr"></i> @lang('ip.save')
                </button>
            </div>
        </div>
    </div>
</div>
