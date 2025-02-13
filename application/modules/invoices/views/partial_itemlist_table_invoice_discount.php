<?php
// Same for responsive & table
$invoice_disabled = $invoice->is_read_only != 1 ? '' : ' disabled="disabled"';
?>
            <tr>
                <td class="td-vert-middle"><?php _trans('global_discount'); ?></td>
                <td class="clearfix">
                    <div class="discount-field">
                        <div class="input-group input-group-sm">
                            <input id="invoice_discount_amount" name="invoice_discount_amount" aria-label="<?php _trans('global_discount'); ?>"
                                   value="<?php echo format_amount($invoice->invoice_discount_amount != 0 ? $invoice->invoice_discount_amount : ''); ?>"
                                   class="discount-option form-control input-sm amount"<?php echo $invoice_disabled; ?>>
                            <span class="input-group-addon"><?php echo get_setting('currency_symbol'); ?></span>
                        </div>
                    </div>
                    <div class="discount-field">
                        <div class="input-group input-group-sm">
                            <input id="invoice_discount_percent" name="invoice_discount_percent" aria-label="<?php _trans('global_discount'); ?> %"
                                   value="<?php echo format_amount($invoice->invoice_discount_percent != 0 ? $invoice->invoice_discount_percent : ''); ?>"
                                   class="discount-option form-control input-sm amount"<?php echo $invoice_disabled; ?>>
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </td>
            </tr>