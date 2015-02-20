<script type="text/javascript">
    $(function()
    {
        // Display the create invoice modal
        $('#modal-choose-items').modal('show');

        // Creates the invoice
        $('#select-items-confirm').click(function()
        {
            var product_ids = [];

            $("input[name='product_ids[]']:checked").each(function ()
            {
                product_ids.push(parseInt($(this).val()));
            });

            $.post("<?php echo site_url('products/ajax/process_product_selections'); ?>", {
                product_ids: product_ids
            }, function(data) {
                items = JSON.parse(data);

                for(var key in items) {
                    if ($('#item_table tr:last input[name=item_name]').val() !== '') {
                        $('#new_item').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
                    }
                    $('#item_table tr:last input[name=item_name]').val(items[key].product_name);
                    $('#item_table tr:last textarea[name=item_description]').val(items[key].product_description);
                    $('#item_table tr:last input[name=item_price]').val(items[key].product_price);
                    $('#item_table tr:last input[name=item_quantity]').val('1');
                    $('#item_table tr:last select[name=item_tax_rate_id]').val(items[key].tax_rate_id);

                    $('#modal-choose-items').modal('hide');
                }
            });
        });
		
		// Toggle checkbox when click on row
		$('#products_table tr').click(function(event) {
			if (event.target.type !== 'checkbox') {
				$(':checkbox', this).trigger('click');
			}
		});
    });

</script>

<div id="modal-choose-items" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
     role="dialog" aria-labelledby="modal-choose-items" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close">x</a>
            <h3><?php echo lang('add_product'); ?></h3>
        </div>
        <div class="modal-body">
<<<<<<< HEAD
			<div class="form-inline">
				<div class="form-group filter-form">
					<!-- ToDo
					<select name="filter_family" id="filter_family" class="form-control">
						<option value=""><?php echo lang('any_family'); ?></option>
						<?php foreach ($families as $family) { ?>
						<option value="<?php echo $family->family_id; ?>"
							<?php if($family->family_id == $filter_family) echo ' selected="selected"'; ?>><?php echo $family->family_name; ?></option>
						<?php } ?>
					</select>
					-->
				</div>
				<div class="form-group">
					<input type="text" class="form-control" name="filter_product" id="filter_product" placeholder="<?php echo lang('product_name'); ?>" value="<?php echo $filter_product ?>">
				</div>
				<button type="button" id="filter-button" class="btn btn-default"><?php echo lang('search_product'); ?></button>
				<!-- ToDo
				<button type="button" id="reset-button" class="btn btn-default"><?php echo lang('reset'); ?></button>
				-->
			</div>
			<br />

=======
>>>>>>> parent of 7fa3386... Product filter in modal
            <div class="table-responsive">
                <table id="products_table" class="table table-bordered table-striped">
					<tr>
						<th><?php echo lang('product_sku'); ?></th>
						<th><?php echo lang('family_name'); ?></th>
						<th><?php echo lang('product_name'); ?></th>
						<th><?php echo lang('product_description'); ?></th>
						<th class="text-right"><?php echo lang('product_price'); ?></th>
					</tr>
                    <?php foreach ($products as $product) { ?>
                        <tr>
                            <td class="text-left">
                                <input type="checkbox" name="product_ids[]"
                                       value="<?php echo $product->product_id; ?>">
								<b><?php echo $product->product_sku; ?></b>
                            </td>
                            <td>
                                <b><?php echo $product->product_name; ?></b>
                            </td>
                            <td>
                                <b><?php echo $product->family_name; ?></b>
                            </td>
                            <td>
                                <?php echo $product->product_description; ?>
                            </td>
                            <td class="text-right">
                                <?php echo format_currency($product->product_price); ?>
                            </td>
                        </tr>
                        <!-- Todo
						<tr class="bold-border">
                            <td colspan="3">
                                <?php echo $product->product_description; ?>
                            </td>
                        </tr>
						-->
                    <?php } ?>
                </table>
            </div>
        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                    <?php echo lang('cancel'); ?>
                </button>
                <button class="btn btn-success" id="select-items-confirm" type="button">
                    <i class="fa fa-check"></i>
                    <?php echo lang('submit'); ?>
                </button>
            </div>
        </div>

    </form>

</div>