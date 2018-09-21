<script>
    $(function () {
        $('#btn-add-contact').click(function () {
            $('#modal-placeholder').load('{{ route('clients.contacts.create', [$clientId]) }}');
        });

        $('.btn-edit-contact').click(function () {
            $('#modal-placeholder').load($(this).data('url'));
        });

        $('.btn-delete-contact').click(function () {
            if (confirm('@lang('ip.delete_record_warning')')) {
                $.post('{{ route('clients.contacts.delete', [$clientId]) }}', {
                    id: $(this).data('contact-id')
                }).done(function (response) {
                    $('#tab-contacts').html(response);
                });
            }
        });

        $('.update-default').click(function () {
            $(this).attr('disabled', true);

            $.post('{{ route('clients.contacts.updateDefault', [$clientId]) }}', {
                id: $(this).data('contact-id'),
                default: $(this).data('default')
            }).done(function (response) {
                $('#tab-contacts').html(response);
            });
        });
    });
</script>

<div class="form-group text-right">
    <a href="javascript:void(0)" class="btn btn-primary btn-sm" id="btn-add-contact">
        <i class="fa fa-plus fa-mr"></i> @lang('ip.add_contact')
    </a>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
        <tr>
            <th>@lang('ip.name')</th>
            <th>@lang('ip.email')</th>
            <th>@lang('ip.default_to')</th>
            <th>@lang('ip.default_cc')</th>
            <th>@lang('ip.default_bcc')</th>
            <th>@lang('ip.options')</th>
        </tr>
        </thead>
        <tbody>
        @foreach($contacts as $contact)
            <tr>
                <td>{{ $contact->name }}</td>
                <td>{{ $contact->email }}</td>
                <td>
                    <a href="javascript:void(0)" class="update-default" data-default="to"
                        data-contact-id="{{ $contact->id }}" title="@lang('ip.change_default_to')">
                        {{ $contact->formatted_default_to }}
                    </a>
                </td>
                <td>
                    <a href="javascript:void(0)" class="update-default" data-default="cc"
                        data-contact-id="{{ $contact->id }}" title="@lang('ip.change_default_cc')">
                        {{ $contact->formatted_default_cc }}
                    </a>
                </td>
                <td>
                    <a href="javascript:void(0)" class="update-default" data-default="bcc"
                        data-contact-id="{{ $contact->id }}" title="@lang('ip.change_default_bcc')">
                        {{ $contact->formatted_default_bcc }}
                    </a>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            @lang('ip.options') <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="javascript:void(0)" class="dropdown-item btn-edit-contact"
                                data-url="{{ route('clients.contacts.edit', [$clientId, $contact->id]) }}">
                                <i class="fa fa-edit"></i> @lang('ip.edit')
                            </a>
                            <a href="javascript:void(0)" class="dropdown-item btn-delete-contact"
                                data-contact-id={{ $contact->id }}>
                                <i class="fa fa-trash-o"></i> @lang('ip.delete')
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
