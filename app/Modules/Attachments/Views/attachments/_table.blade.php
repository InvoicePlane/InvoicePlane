<script>
    $(function () {
        $('.btn-delete-attachment').click(function () {
            if (confirm('@lang('ip.delete_record_warning')')) {
                $.post("{{ route('attachments.ajax.delete') }}", {
                    model: '{{ addslashes($model) }}',
                    model_id: '{{ $object->id }}',
                    attachment_id: $(this).data('attachment-id')
                }, function () {
                    $('#attachments-list').load("{{ route('attachments.ajax.list') }}", {
                        model: '{{ addslashes($model) }}',
                        model_id: '{{ $object->id }}'
                    });
                });
            }
        });

        $('.client-visibility').change(function () {
            $.post('{{ route('attachments.ajax.access.update') }}', {
                client_visibility: $(this).val(),
                attachment_id: $(this).data('attachment-id')
            });
        });

        $('#btn-attach-files').click(function () {
            $('#modal-placeholder').load('{{ route('attachments.ajax.modal') }}', {
                model: '{{ addslashes($model) }}',
                model_id: '{{ $object->id }}'
            });
        });
    });
</script>

<div id="attachments-list">

    <div class="form-group text-right">
        <a href="javascript:void(0)" id="btn-attach-files" class="btn btn-primary btn-sm">
            <i class="fa fa-paperclip fa-mr"></i> @lang('ip.attach_files')
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>@lang('ip.attachment')</th>
                <th>@lang('ip.mime_type')</th>
                <th>@lang('ip.file_size')</th>
                <th>@lang('ip.client_visibility')</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($object->attachments()->orderBy('created_at', 'desc')->get() as $attachment)
                <tr>
                    <td>
                        <a href="{{ $attachment->download_url }}">
                            {{ $attachment->filename }}
                        </a>
                    </td>
                    <td>{{ $attachment->mimetype }}</td>
                    <td>{{ $attachment->getFormattedSize() }}</td>
                    <td>
                        {!! Form::select(
                            '',
                            $object->attachment_permission_options,
                            $attachment->client_visibility,
                            ['class' => 'form-control form-control-sm client-visibility', 'data-attachment-id' => $attachment->id]
                        ) !!}
                    </td>
                    <td>
                        <a class="btn btn-sm btn-outline-danger btn-delete-attachment" href="javascript:void(0);"
                            title="@lang('ip.delete')" data-attachment-id="{{ $attachment->id }}">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
