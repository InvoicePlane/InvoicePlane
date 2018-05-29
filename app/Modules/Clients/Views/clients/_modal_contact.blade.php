<script type="text/javascript">
  $(function () {
    var modalContact = $('#modal-contact');

    modalContact.modal();

    $('#btn-contact-submit').click(function () {
      $.post("{{ $submitRoute }}", {
        client_id: {{ $clientId }},
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

<div class="modal" id="modal-contact">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">
                    @if ($editMode)
                        {{ trans('fi.edit_contact') }}
                    @else
                        {{ trans('fi.add_contact') }}
                    @endif
                </h4>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <form class="form-horizontal">

                    <div class="form-group">
                        <label class="col-sm-3 control-label">{{ trans('fi.name') }}:</label>
                        <div class="col-sm-9">
                            {!! Form::text('contact_name', ($editMode) ? $contact->name : null, ['class' => 'form-control', 'id' => 'contact_name']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">{{ trans('fi.email') }}:</label>
                        <div class="col-sm-9">
                            {!! Form::text('contact_email', ($editMode) ? $contact->email : null, ['class' => 'form-control', 'id' => 'contact_email']) !!}
                        </div>
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                <button type="button" id="btn-contact-submit" class="btn btn-primary">{{ trans('fi.save') }}</button>
            </div>
        </div>
    </div>
</div>