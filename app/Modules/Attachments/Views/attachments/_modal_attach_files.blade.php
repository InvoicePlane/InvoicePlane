<script>
    $(function () {
        $('#modal-attach-files').modal({
            backdrop: 'static',
            keyboard: false
        });

        $('#input-attachments').change(function () {

            formData = new FormData(document.forms.namedItem('form-attachments'));
            formData.append('model', '{{ addslashes($model) }}');
            formData.append('model_id', '{{ $modelId }}');

            $('#input-attachments').attr('disabled', 'disabled');
            resetProgressBar('0%', '0%');
            $('#attachment-upload-progress').show();

            $.ajax({
                url: '{{ route('attachments.ajax.upload') }}',
                type: 'POST',
                data: formData,
                xhr: function () {
                    var myXhr = $.ajaxSettings.xhr();
                    if (myXhr.upload) {
                        myXhr.upload.addEventListener('progress', progress, false);
                    }
                    return myXhr;
                },
                cache: false,
                contentType: false,
                processData: false,
                success: function (data, textStatus, jqXHR) {
                    $('#input-attachments').val('');
                    $('#attachments-list').load("{{ route('attachments.ajax.list') }}", {
                        model: '{{ addslashes($model) }}',
                        model_id: '{{ $modelId }}'
                    });
                    $('#input-attachments').removeAttr('disabled');
                    $('#modal-attach-files').modal('hide');
                },
                error: function (XMLHttpRequest, textStatus, error) {
                    $('#attachment-upload-progress-bar').addClass('progress-bar-danger').html(error);
                    $('#input-attachments').removeAttr('disabled');
                }
            });

        });

        function progress (e) {
            if (e.lengthComputable) {
                var max = e.total;
                var current = e.loaded;
                var Percentage = Math.round((current * 100) / max);
                $('#attachment-upload-progress-bar').css('width', Percentage + '%').html(Percentage + '%');

                if (Percentage === 100) {
                    resetProgressBar('100%', '@lang('ip.complete')');
                    $('#attachment-upload-progress-bar').addClass('progress-bar-success').html('@lang('ip.complete')');
                }
            }
        }

        function resetProgressBar (width, text) {
            $('#attachment-upload-progress-bar')
                .removeClass('progress-bar-danger')
                .removeClass('progress-bar-success')
                .css('width', width)
                .html(text);
        }
    });
</script>

<div id="modal-attach-files" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <h5 class="modal-title">@lang('ip.attach_files')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="@lang('ip.close')">
                    <span aria-hidden="true"><i class="fa fa-times"></i></span>
                </button>

            </div>
            <div class="modal-body">

                <p class="text-bold">@lang('ip.attach_files')</p>

                <form method="post" id="form-attachments" enctype="multipart/form-data" name="form-attachments"
                    class="mb-4">
                    <input type="file" name="attachments[]" id="input-attachments" multiple>
                </form>

                <div style="display: none;" id="attachment-upload-progress">
                    <p class="text-bold">@lang('ip.upload_progress')</p>

                    <div class="progress">
                        <div id="attachment-upload-progress-bar" class="progress-bar" role="progressbar"
                            style="width: 0;">
                            0%
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">@lang('ip.cancel')</button>
            </div>
        </div>
    </div>
</div>
