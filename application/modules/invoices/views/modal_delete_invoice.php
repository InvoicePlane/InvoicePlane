<script>
    $(function () {
        $('#modal_delete_invoice_confirm').click(function () {
            invoice_id = $(this).data('invoice-id');
            window.location = '<?php echo site_url('invoices/delete'); ?>/' + invoice_id;
        });
    });
</script>

<div id="delete-invoice" class="modal modal-lg" role="dialog" aria-labelledby="delete-invoice" aria-hidden="true">

    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title"><?php _trans('delete_invoice'); ?></h4>
        </div>
        <div class="modal-body">

            <div class="alert alert-danger"><?php _trans('delete_invoice_warning'); ?></div>

        </div>
        <div class="modal-footer">
            <div class="btn-group">
                <a href="#" id="modal_delete_invoice_confirm" class="btn btn-danger"
                   data-invoice-id="<?php echo $invoice->invoice_id; ?>">
                    <i class="fa fa-trash-o"></i>
                    <?php echo trans('confirm_deletion') ?>
                </a>
                <a href="#" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
                </a>
            </div>
        </div>
    </div>

</div>
