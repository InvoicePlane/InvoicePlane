<script type="text/javascript">
    $(function()
    {
        // Display the create invoice modal
        $('#modal-choose-items').modal('show');

        // Creates the invoice
        $('#select-items-confirm').click(function()
        {
            var item_lookup_ids = [];
            
            $("input[name='item_lookup_ids[]']:checked").each(function ()
            {
                item_lookup_ids.push(parseInt($(this).val()));
            });
            
            $.post("<?php echo site_url('item_lookups/ajax/process_item_selections'); ?>", {
                item_lookup_ids: item_lookup_ids
            }, function(data) {
                items = JSON.parse(data);

                for(var key in items) {
                    if ($('#item_table tr:last input[name=item_name]').val() !== '') {
                        $('#new_item').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
                    }
                    $('#item_table tr:last input[name=item_name]').val(items[key].item_name);
                    $('#item_table tr:last textarea[name=item_description]').val(items[key].item_description);
                    $('#item_table tr:last input[name=item_price]').val(items[key].item_price);
                    $('#item_table tr:last input[name=item_quantity]').val('1');
                    
                    $('#modal-choose-items').modal('hide');
                }
            });
        });
    });

</script>

<div id="modal-choose-items" class="modal hide">
	<form class="form-horizontal">
		<div class="modal-header">
			<a data-dismiss="modal" class="close">x</a>
			<h3><?php echo lang('add_item_from_lookup'); ?></h3>
		</div>
		<div class="modal-body">
			
            <table class="table table-bordered table-striped">
                <tr>
                    <th></th>
                    <th><?php echo lang('item'); ?></th>
                    <th><?php echo lang('description'); ?></th>
                    <th><?php echo lang('price'); ?></th>
                </tr>
                <?php foreach ($item_lookups as $item_lookup) { ?>
                <tr>
                    <td><input type="checkbox" name="item_lookup_ids[]" value="<?php echo $item_lookup->item_lookup_id; ?>"></td>
                    <td><?php echo $item_lookup->item_name; ?></td>
                    <td><?php echo $item_lookup->item_description; ?></td>
                    <td><?php echo format_currency($item_lookup->item_price); ?></td>
                </tr>
                <?php } ?>
            </table>

		</div>

		<div class="modal-footer">
            <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="icon-white icon-remove"></i> <?php echo lang('cancel'); ?></button>
			<button class="btn btn-primary" id="select-items-confirm" type="button"><i class="icon-white icon-ok"></i> <?php echo lang('submit'); ?></button>
		</div>

	</form>

</div>