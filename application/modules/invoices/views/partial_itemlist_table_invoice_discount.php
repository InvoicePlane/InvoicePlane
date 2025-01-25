<?php // same for responsive & table
$invoice_disabled = $invoice->is_read_only != 1 ? '' : ' disabled="disabled"';
?>
            <tr>
                <td class="td-vert-middle"><?php _trans('discount'); ?></td>
                <td class="clearfix">
                    <div class="discount-field">
                        <div class="input-group input-group-sm">
                            <input id="invoice_discount_amount" name="invoice_discount_amount"
                                   value="<?php echo format_amount($invoice->invoice_discount_amount != 0 ? $invoice->invoice_discount_amount : ''); ?>"
                                   class="discount-option form-control input-sm amount"<?php echo $invoice_disabled; ?>>
                            <div class="input-group-addon"><?php echo get_setting('currency_symbol'); ?></div>
                        </div>
                    </div>
                    <div class="discount-field">
                        <div class="input-group input-group-sm">
                            <input id="invoice_discount_percent" name="invoice_discount_percent"
                                   value="<?php echo format_amount($invoice->invoice_discount_percent != 0 ? $invoice->invoice_discount_percent : ''); ?>"
                                   class="discount-option form-control input-sm amount"<?php echo $invoice_disabled; ?>>
                            <div class="input-group-addon">&percnt;</div>
                        </div>
                    </div>
                </td>
            </tr>