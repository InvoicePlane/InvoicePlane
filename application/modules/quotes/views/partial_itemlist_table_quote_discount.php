            <tr>
                <td class="td-vert-middle"><?php _trans('global_discount'); ?></td>
                <td class="clearfix">
                    <div class="discount-field">
                        <div class="input-group input-group-sm">
                            <input id="quote_discount_amount" name="quote_discount_amount"
                                   class="discount-option form-control input-sm amount" aria-label="<?php _trans('global_discount'); ?>"
                                   value="<?php echo format_amount($quote->quote_discount_amount != 0 ? $quote->quote_discount_amount : ''); ?>">
                            <span class="input-group-addon"><?php echo get_setting('currency_symbol'); ?></span>
                        </div>
                    </div>
                    <div class="discount-field">
                        <div class="input-group input-group-sm">
                            <input id="quote_discount_percent" name="quote_discount_percent" aria-label="<?php _trans('global_discount'); ?> %"
                                   value="<?php echo format_amount($quote->quote_discount_percent != 0 ? $quote->quote_discount_percent : ''); ?>"
                                   class="discount-option form-control input-sm amount">
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </td>
            </tr>
