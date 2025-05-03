<script>
    $(function () {
        // Select2 for all select inputs
        $(".simple-select").select2();

        $('#invoice_tax_submit').click(function () {
            $.post("<?php echo site_url('invoices/ajax/save_invoice_tax_rate'); ?>", {
                    invoice_id: <?php echo $invoice_id; ?>,
                    tax_rate_id: $('#tax_rate_id').val(),
                    include_item_tax: $('#include_item_tax').val()
                },
                function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);
                    if (response.success === 1) {
                        window.location = "<?php echo site_url('invoices/view'); ?>/" + <?php echo $invoice_id; ?>;
                    }
                });
        });
    });
</script>

<div id="add-invoice-tax" class="modal modal-lg" role="dialog" aria-labelledby="add-invoice-tax" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title"><?php _trans('add_invoice_tax'); ?></h4>
        </div>
        <div class="modal-body">

            <div class="form-group">
                <label for="tax_rate_id"><?php _trans('invoice_tax_rate'); ?>: </label>
                <select name="tax_rate_id" id="tax_rate_id" class="form-control simple-select">
                    <option value="0"><?php _trans('none'); ?></option>
                    <?php foreach ($tax_rates as $tax_rate) { ?>
                        <option value="<?php echo $tax_rate->tax_rate_id; ?>">
                            <?php echo format_amount($tax_rate->tax_rate_percent) . '% - ' . htmlsc($tax_rate->tax_rate_name); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="include_item_tax"><?php _trans('tax_rate_placement'); ?></label>
                <select name="include_item_tax" id="include_item_tax" class="form-control simple-select">
                    <option value="0"><?php _trans('apply_before_item_tax'); ?></option>
                    <option value="1"><?php _trans('apply_after_item_tax'); ?></option>
                </select>
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-success" id="invoice_tax_submit" type="button">
                    <i class="fa fa-check"></i> <?php _trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
