<script type="text/javascript">
    $(function () {
        $('#modal_delete_invoice_confirm').click(function () {
            // alert($(this).data('invoice-id'));
            invoice_id = $(this).data('invoice-id');
            window.location = '<?php echo site_url('invoices/delete'); ?>/' + invoice_id;
        });
    });
</script>

<div id="delete-invoice" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
     role="dialog" aria-labelledby="delete-invoice" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3><?php echo trans('delete_invoice'); ?></h3>
        </div>
        <div class="modal-body">
            <p class="alert alert-danger"><?php echo trans('delete_invoice_warning'); ?></p>
        </div>
        <div class="modal-footer">
            <div class="btn-group">
                <a href="#" id="modal_delete_invoice_confirm" class="btn btn-danger"
                   data-invoice-id="<?php echo $invoice->invoice_id; ?>">
                    <i class="fa fa-trash-o"></i>
                    <?php echo trans('confirm_deletion') ?>
                </a>

                <a href="#" class="btn btn-success" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php echo trans('no'); ?>
                </a>
            </div>
        </div>
    </div>
</div>