<script type="text/javascript">
    $(function () {
        $('#invoice_tax_submit').click(function () {
            $.post("<?php echo site_url('invoices/ajax/save_invoice_tax_rate'); ?>", {
                    invoice_id: <?php echo $invoice_id; ?>,
                    tax_rate_id: $('#tax_rate_id').val(),
                    include_item_tax: $('#include_item_tax').val()
                },
                function (data) {
                    var response = JSON.parse(data);
                    if (response.success == 1) {
                        window.location = "<?php echo site_url('invoices/view'); ?>/" + <?php echo $invoice_id; ?>;
                    }
                });
        });
    });
</script>

<div id="add-invoice-tax" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
     role="dialog" aria-labelledby="add-invoice-tax" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3><?php echo lang('add_invoice_tax'); ?></h3>
        </div>
        <div class="modal-body">

            <div class="form-group">
                <label for="tax_rate_id"><?php echo lang('invoice_tax_rate'); ?>: </label>

                <div class="controls">
                    <select name="tax_rate_id" id="tax_rate_id" class="form-control">
                        <option value="0"><?php echo lang('none'); ?></option>
                        <?php foreach ($tax_rates as $tax_rate) { ?>
                            <option
                                value="<?php echo $tax_rate->tax_rate_id; ?>"><?php echo $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="include_item_tax"><?php echo lang('tax_rate_placement'); ?></label>

                <div class="controls">
                    <select name="include_item_tax" id="include_item_tax" class="form-control">
                        <option value="0"><?php echo lang('apply_before_item_tax'); ?></option>
                        <option value="1"><?php echo lang('apply_after_item_tax'); ?></option>
                    </select>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php echo lang('cancel'); ?>
                </button>
                <button class="btn btn-success" id="invoice_tax_submit" type="button">
                    <i class="fa fa-check"></i> <?php echo lang('submit'); ?>
                </button>
            </div>
        </div>

    </form>

</div>