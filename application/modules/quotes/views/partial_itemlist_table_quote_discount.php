            <tr>
                <td class="td-vert-middle"><?php _trans('discount'); ?></td>
                <td class="clearfix">
                    <div class="discount-field">
                        <div class="input-group input-group-sm">
                            <label for="quote_discount_amount" class="input-group-addon ig-addon-aligned"><?php _trans('discount'); ?></label>
                            <input id="quote_discount_amount" name="quote_discount_amount"
                                   class="discount-option form-control input-sm amount"
                                   value="<?php echo format_amount($quote->quote_discount_amount != 0 ? $quote->quote_discount_amount : ''); ?>">
                            <div class="input-group-addon"><?php echo get_setting('currency_symbol'); ?></div>
                        </div>
                    </div>
                    <div class="discount-field">
                        <div class="input-group input-group-sm">
                            <label for="quote_discount_percent" class="input-group-addon ig-addon-aligned"><?php _trans('discount'); ?> %</label>
                            <input id="quote_discount_percent" name="quote_discount_percent"
                                   value="<?php echo format_amount($quote->quote_discount_percent != 0 ? $quote->quote_discount_percent : ''); ?>"
                                   class="discount-option form-control input-sm amount">
                            <div class="input-group-addon">&percnt;</div>
                        </div>
                    </div>
                </td>
            </tr>
