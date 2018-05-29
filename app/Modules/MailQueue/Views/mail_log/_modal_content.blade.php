<script type="text/javascript">
  $(function () {
    $('#modal-mail-content').modal();
  });
</script>

<div class="modal fade" id="modal-mail-content">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">{{ $mail->subject }}</h4>
            </div>
            <div class="modal-body">

                {!! $mail->body !!}

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('fi.close') }}</button>
            </div>
        </div>
    </div>
</div>