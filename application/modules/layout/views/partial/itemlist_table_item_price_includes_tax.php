<?php
// Called from inside the price <td> in [quotes|invoices]/partial_itemlist_table.php (item & new row).
// Checked: item_price above is the final, tax-inclusive price; Mdl_item_amounts /
// Mdl_quote_item_amounts derive the base price and tax from it instead of adding tax on top.
$invoice_disabled = isset($invoice) && $invoice->is_read_only == 1 ? ' disabled="disabled"' : '';
$item_checked     = isset($item) && ! empty($item->item_price_includes_tax) ? ' checked="checked"' : '';
?>
                <div class="checkbox" style="margin: 4px 0 0;">
                    <label>
                        <input type="checkbox" name="item_price_includes_tax" value="1"<?php echo $item_checked; ?><?php echo $invoice_disabled; ?>>
                        <small data-toggle="tooltip" data-placement="bottom" title="<?php _trans('item_price_includes_tax_hint'); ?>">
                            <?php _trans('item_price_includes_tax'); ?>
                        </small>
                    </label>
                </div>
