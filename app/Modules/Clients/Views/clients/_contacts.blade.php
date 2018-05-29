<script type="text/javascript">
  $(function () {
    $('#btn-add-contact').click(function () {
      $('#modal-placeholder').load('{{ route('clients.contacts.create', [$clientId]) }}');
    });

    $('.btn-edit-contact').click(function () {
      $('#modal-placeholder').load($(this).data('url'));
    });

    $('.btn-delete-contact').click(function () {
      if (confirm('{{ trans('fi.delete_record_warning') }}')) {
        $.post('{{ route('clients.contacts.delete', [$clientId]) }}', {
          id: $(this).data('contact-id')
        }).done(function (response) {
          $('#tab-contacts').html(response);
        });
      }
    });

    $('.update-default').click(function () {
      $.post('{{ route('clients.contacts.updateDefault', [$clientId]) }}', {
        id: $(this).data('contact-id'),
        default: $(this).data('default')
      }).done(function (response) {
        $('#tab-contacts').html(response);
      });
    });
  });
</script>

<div class="row">
    <div class="col-lg-12">
        <div class="pull-right">
            <a href="javascript:void(0)" class="btn btn-primary btn-sm" id="btn-add-contact"><i
                        class="fa fa-plus"></i> {{ trans('fi.add_contact') }}</a>
        </div>
        <table class="table table-hover">
            <thead>
            <tr>
                <th>{{ trans('fi.name') }}</th>
                <th>{{ trans('fi.email') }}</th>
                <th>{{ trans('fi.default_to') }}</th>
                <th>{{ trans('fi.default_cc') }}</th>
                <th>{{ trans('fi.default_bcc') }}</th>
                <th>{{ trans('fi.options') }}</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($contacts as $contact) { ?>
            <tr>
                <td>{{ $contact->name }}</td>
                <td>{{ $contact->email }}</td>
                <td><a href="javascript:void(0)" class="update-default" data-default="to"
                       data-contact-id="{{ $contact->id }}">{{ $contact->formatted_default_to }}</a></td>
                <td><a href="javascript:void(0)" class="update-default" data-default="cc"
                       data-contact-id="{{ $contact->id }}">{{ $contact->formatted_default_cc }}</a></td>
                <td><a href="javascript:void(0)" class="update-default" data-default="bcc"
                       data-contact-id="{{ $contact->id }}">{{ $contact->formatted_default_bcc }}</a></td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                            {{ trans('fi.options') }} <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a href="javascript:void(0)" class="btn-edit-contact"
                                   data-url="{{ route('clients.contacts.edit', [$clientId, $contact->id]) }}"><i
                                            class="fa fa-edit"></i> {{ trans('fi.edit') }}</a></li>
                            <li><a href="javascript:void(0)" class="btn-delete-contact"
                                   data-contact-id={{ $contact->id }}><i
                                            class="fa fa-trash-o"></i> {{ trans('fi.delete') }}</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>

    </div>
</div>