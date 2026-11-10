<script>
    $(function () {
        $('#modal_related_quote').modal('show');

        // When a quote is selected, copy its reference values into the invoice form
        $('.select-related-quote').click(function () {
            var $btn = $(this);
            $('#invoice_quote_number').val($btn.data('quote-number'));
            $('#invoice_work_order').val($btn.data('work-order'));
            $('#invoice_agreement').val($btn.data('agreement'));
            $('#modal_related_quote').modal('hide');
        });
    });
</script>

<div id="modal_related_quote" class="modal modal-lg" role="dialog" aria-labelledby="modal_related_quote"
     aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title"><?php _trans('select_related_quote'); ?></h4>
        </div>
        <div class="modal-body">
<?php if (empty($quotes)) : ?>
            <div class="alert alert-info no-margin">
                <?php _trans('no_quotes_for_client'); ?>
            </div>
<?php else : ?>
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th><?php _trans('quote'); ?> #</th>
                        <th><?php _trans('quote_work_order'); ?></th>
                        <th><?php _trans('quote_agreement'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
<?php foreach ($quotes as $quote) : ?>
                    <tr>
                        <td><?php echo htmlsc($quote->quote_number); ?></td>
                        <td><?php echo htmlsc($quote->quote_work_order); ?></td>
                        <td><?php echo htmlsc($quote->quote_agreement); ?></td>
                        <td class="text-right">
                            <button type="button" class="btn btn-sm btn-primary select-related-quote"
                                    data-quote-number="<?php echo html_escape($quote->quote_number); ?>"
                                    data-work-order="<?php echo html_escape($quote->quote_work_order); ?>"
                                    data-agreement="<?php echo html_escape($quote->quote_agreement); ?>">
                                <i class="fa fa-check fa-fw"></i> <?php _trans('select'); ?>
                            </button>
                        </td>
                    </tr>
<?php endforeach; ?>
                </tbody>
            </table>
<?php endif; ?>
        </div>
        <div class="modal-footer">
            <button class="btn btn-danger" type="button" data-dismiss="modal">
                <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
            </button>
        </div>
    </div>
</div>
