<table id="item_table" class="items table table-striped table-bordered">
	<thead>
		<tr>
			<th><?php echo lang('item'); ?></th>
			<th style="min-width: 300px;"><?php echo lang('description'); ?></th>
			<th style="width: 100px;"><?php echo lang('quantity'); ?></th>
			<th style="width: 100px;"><?php echo lang('price'); ?></th>
			<th><?php echo lang('tax_rate'); ?></th>
			<th><?php echo lang('subtotal'); ?></th>
			<th><?php echo lang('tax'); ?></th>
			<th><?php echo lang('total'); ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		
		<tr id="new_item" style="display: none;">
			<td style="vertical-align: top;">
				<input type="hidden" name="quote_id" value="<?php echo $quote_id; ?>">
				<input type="hidden" name="item_id" value="">
				<input type="text" name="item_name" class="lookup-item-name" style="width: 90%;" data-typeahead=""><br>
                <label><input type="checkbox" name="save_item_as_lookup" tabindex="999"> <?php echo lang('save_item_as_lookup'); ?></label>
			</td>
            <td><textarea name="item_description" style="width: 90%;"></textarea></td>
			<td style="vertical-align: top;"><input type="text" class="input-mini" name="item_quantity" style="width: 90%;" value=""></td>
			<td style="vertical-align: top;"><input type="text" class="input-mini" name="item_price" style="width: 90%;" value=""></td>
			<td style="vertical-align: top;">
				<select name="item_tax_rate_id" class="input-small">
					<option value="0"><?php echo lang('none'); ?></option>
					<?php foreach ($tax_rates as $tax_rate) { ?>
					<option value="<?php echo $tax_rate->tax_rate_id; ?>" <?php if ($tax_rate->tax_rate_id == $this->mdl_settings->setting('default_item_tax_rate')) { ?>selected="selected"<?php } ?>><?php echo $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?></option>
					<?php } ?>
				</select>
			</td>
			<td style="vertical-align: top;"><span name="subtotal"></span></td>
			<td style="vertical-align: top;"><span name="item_tax_total"></span></td>
			<td style="vertical-align: top;"><span name="item_total"></span></td>
			<td></td>
		</tr>
		
		<?php foreach ($items as $item) { ?>
		<tr class="item">
			<td style="vertical-align: top;">
				<input type="hidden" name="quote_id" value="<?php echo $quote_id; ?>">
				<input type="hidden" name="item_id" value="<?php echo $item->item_id; ?>">
				<input type="text" name="item_name" style="width: 90%;" value="<?php echo $item->item_name; ?>">
			</td>
            <td><textarea name="item_description" style="width: 90%;"><?php echo $item->item_description; ?></textarea></td>
			<td style="vertical-align: top;"><input type="text" name="item_quantity" style="width: 90%;" value="<?php echo format_amount($item->item_quantity); ?>"></td>
			<td style="vertical-align: top;"><input type="text" name="item_price" style="width: 90%;" value="<?php echo format_amount($item->item_price); ?>"></td>
			<td style="vertical-align: top;">
				<select name="item_tax_rate_id" name="item_tax_rate_id" style="width: 90%;">
					<option value="0"><?php echo lang('none'); ?></option>
					<?php foreach ($tax_rates as $tax_rate) { ?>
					<option value="<?php echo $tax_rate->tax_rate_id; ?>" <?php if ($item->item_tax_rate_id == $tax_rate->tax_rate_id) { ?>selected="selected"<?php } ?>><?php echo $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?></option>
					<?php } ?>
				</select>
			</td>
			<td style="vertical-align: top;"><span name="subtotal"><?php echo format_currency($item->item_subtotal); ?></span></td>
			<td style="vertical-align: top;"><span name="item_tax_total"><?php echo format_currency($item->item_tax_total); ?></span></td>
			<td style="vertical-align: top;"><span name="item_total"><?php echo format_currency($item->item_total); ?></span></td>
			<td style="vertical-align: top;">
				<a class="" href="<?php echo site_url('quotes/delete_item/' . $quote->quote_id . '/' . $item->item_id); ?>" title="<?php echo lang('delete'); ?>">
					<i class="icon-remove"></i>
				</a>
			</td>
		</tr>
		<?php } ?>
		
	</tbody>

</table>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th><?php echo lang('subtotal'); ?></th>
			<th><?php echo lang('item_tax'); ?></th>
			<th><?php echo lang('quote_tax'); ?></th>
			<th><?php echo lang('total'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo format_currency($quote->quote_item_subtotal); ?></td>
			<td><?php echo format_currency($quote->quote_item_tax_total); ?></td>
			<td>
				<?php if ($quote_tax_rates) { foreach ($quote_tax_rates as $quote_tax_rate) { ?>
					<strong><?php echo anchor('quotes/delete_quote_tax/' . $quote->quote_id . '/' . $quote_tax_rate->quote_tax_rate_id, lang('remove')) . ' ' . $quote_tax_rate->quote_tax_rate_name . ' ' . $quote_tax_rate->quote_tax_rate_percent; ?>%:</strong>				
					<?php echo format_currency($quote_tax_rate->quote_tax_rate_amount); ?><br>
				<?php } } else { echo format_currency('0'); }?>
			</td>
			<td><?php echo format_currency($quote->quote_total); ?></td>
		</tr>
	</tbody>
</table>