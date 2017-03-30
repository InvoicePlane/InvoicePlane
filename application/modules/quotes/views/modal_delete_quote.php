<script>
    $(function () {
        $('#modal_delete_quote_confirm').click(function () {
            quote_id = $(this).data('quote-id');
            window.location = '<?php echo site_url('quotes/delete'); ?>/' + quote_id;
        });
    });
</script>

<div id="delete-quote" class="modal modal-lg" role="dialog" aria-labelledby="modal_delete_quote" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title"><?php echo trans('delete_quote'); ?></h4>
        </div>
        <div class="modal-body">

            <div class="alert alert-danger"><?php echo trans('delete_quote_warning'); ?></div>

        </div>
        <div class="modal-footer">
            <div class="btn-group">
                <button id="modal_delete_quote_confirm" class="btn btn-danger"
                        data-quote-id="<?php echo $quote->quote_id; ?>">
                    <i class="fa fa-trash-o"></i> <?php echo trans('yes'); ?>
                </button>
                <button class="btn btn-success" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php echo trans('no'); ?>
                </button>
            </div>
        </div>
    </div>

</div>
