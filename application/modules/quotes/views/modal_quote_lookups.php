<script>
    $(function () {
        // Display modal
        $('#modal-choose-quote').modal('show');

        $(".simple-select").select2();

        // Creates the invoice
        $('.select-items-confirm').click(function () {

            var quote_id = parseInt($("input[name='quote_id']:checked").val());

            //$.post("<?php echo site_url('quotes/ajax/process_product_selections'); ?>", {
            //$.post("<?php echo site_url('quotes/ajax/modal_quote_lookups_select'); ?>", {

            $.post("<?php echo site_url('quotes/ajax/modal_quote_lookups_select'); ?>", {
                quote_id: quote_id
            }, function (data) {
                <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                var items = JSON.parse(data);

                for (var key in items) {
                    $('#invoice_quote_number').val(items[key].quote_number);
                    $('#invoice_work_order').val(items[key].quote_work_order);
                    $('#invoice_agreement').val(items[key].quote_agreement);

                    $('#modal-choose-quote').modal('hide');
                }
            });
        });

        // Toggle checkbox when click on row
        $(document).on('click', '.quote', function (event) {
            if (event.target.type !== 'radio') {
                $(':radio', this).trigger('click');
            }
        });

    });
</script>

<div id="modal-choose-quote" class="modal col-xs-12 col-sm-10 col-sm-offset-1" role="dialog" aria-labelledby="modal-choose-quote" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title"><?php _trans('select_related_quote'); ?></h4>
        </div>
        <div class="modal-body">
            <div id="product-lookup-table">

                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped">
                        <tr>
                            <th>&nbsp;</th>
                            <th><?php _trans('quote'); ?> </th>
                            <th><?php _trans('status'); ?></th>
                            <th><?php _trans('created'); ?></th>
                            <th><?php _trans('due_date'); ?></th>
                            <th class="text-right"><?php _trans('amount'); ?></th>
                        </tr>

                        <?php 
                        foreach ($related_quotes as $quote) { 
                        ?>
                        <tr class="quote">
                            <td class="text-left">
                                <input type="radio" name="quote_id" value="<?php echo $quote->quote_id; ?>">
                            </td>
                            <td nowrap class="text-left">
                                <b><?php _htmlsc($quote->quote_number); ?></b>
                            </td>
                            <td>
                                <b><?php echo $quote_statuses[$quote->quote_status_id]['label']; ?></b>
                            </td>

                            <td>
                                <b><?php _htmlsc($quote->quote_date_created); ?></b>
                            </td>
                            <td>
                                <b><?php _htmlsc($quote->quote_date_expires); ?></b>
                            </td>
                            <td class="text-right">
                                <?php echo format_currency($quote->quote_total); ?>
                            </td>
                        </tr>
                        <?php 
                        }
                        ?>

                    </table>
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <div class="btn-group">
                <button class="select-items-confirm btn btn-success" type="button">
                    <i class="fa fa-check"></i>
                    <?php _trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                    <?php _trans('cancel'); ?>
                </button>
            </div>
        </div>
    </form>

</div>
