<script type="text/javascript">
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

    @if (!config('app.demo'))
        <a href="javascript:void(0)" class="btn btn-primary btn-sm"
           id="btn-attach-files">@lang('ip.attach_files')</a>
    @else
        <a href="javascript:void(0)" class="btn btn-primary btn-sm">File attachments are disabled in the demo</a>
    @endif

    <table class="table table-condensed">
        <thead>
        <tr>
            <th>@lang('ip.attachment')</th>
            <th>@lang('ip.client_visibility')</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($object->attachments()->orderBy('created_at', 'desc')->get() as $attachment)
            <tr>
                <td><a href="{{ $attachment->download_url }}">{{ $attachment->filename }}</a></td>
                <td>
                    <div class="row">
                        <div class="col-md-4">
                            {!! Form::select('', $object->attachment_permission_options, $attachment->client_visibility, ['class' => 'form-control client-visibility', 'data-attachment-id' => $attachment->id]) !!}
                        </div>
                    </div>
                </td>
                <td>
                    <a class="btn btn-xs btn-default btn-delete-attachment" href="javascript:void(0);"
                       title="@lang('ip.delete')" data-attachment-id="{{ $attachment->id }}">
                        <i class="fa fa-times"></i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>