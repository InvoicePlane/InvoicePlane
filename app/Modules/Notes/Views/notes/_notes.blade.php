<script>
    $(function () {
        $('#btn-create-note').click(function () {
            if ($('#note_content').val() !== '') {
                @if (isset($showPrivateCheckbox) and $showPrivateCheckbox == true)
                    showPrivateCheckbox = 1;
                if ($('#private').prop('checked')) {
                    isPrivate = 1;
                } else {
                    isPrivate = 0;
                }
                @else
                    showPrivateCheckbox = 0;
                isPrivate = 0;
                @endif

                $.post('{{ route('notes.create') }}', {
                    model: '{{ addslashes($model) }}',
                    model_id: '{{ $object->id }}',
                    note: $('#note_content').val(),
                    isPrivate: isPrivate,
                    showPrivateCheckbox: showPrivateCheckbox
                }).done(function (response) {
                    $('#note_content').val('');
                    $('#private').prop('checked', false);
                    $('#notes-list').html(response);
                });
            }
        });

        @if (!auth()->user()->client_id)
        $(document).on('click', '.delete-note', function () {
            noteId = $(this).data('note-id');
            $('#note-' + noteId).hide();
            $.post("{{ route('notes.delete') }}", {
                id: noteId
            });
        });
        @endif
    });
</script>

<div class="card">

    @if (!isset($hideHeader))
        <div class="card-header">
            <h5>@lang('ip.notes')</h5>
        </div>
    @endif

    <div class="card-body">
        <div class="direct-chat-messages" id="notes-list">
            @include('notes._notes_list')
        </div>
    </div>

    <div class="card-footer">

        <textarea class="form-control" id="note_content"
            placeholder="@lang('ip.placeholder_type_message')"></textarea>

        <div class="row mt-2">
            @if (isset($showPrivateCheckbox) && $showPrivateCheckbox === true)
                <div class="col">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="private" id="private">
                        <label class="form-check-label" for="private">
                            @lang('ip.private')
                        </label>
                    </div>
                </div>
            @endif
            <div class="col">
                <button type="button" class="btn btn-sm btn-success" id="btn-create-note">
                    <i class="fa fa-plus fa-mr"></i> @lang('ip.add_note')
                </button>
            </div>
        </div>

    </div>

</div>

<div class="row">
    <div class="col-12">
        <div class="box box-solid direct-chat direct-chat-warning">

            <div class="box-body">

            </div>
            <div class="box-footer">

            </div>
        </div>
    </div>
</div>
