<div class="col-xs-12 col-md-8 col-md-offset-2">

    <div class="panel panel-default">
        <div class="panel-heading">
            <?php _trans('taxes'); ?>
        </div>
        <div class="panel-body">

            <div class="row">
                <div class="col-xs-12 col-md-6">

                    <div class="form-group">
                        <label for="settings[default_invoice_tax_rate]">
                            <?php _trans('default_invoice_tax_rate'); ?>
                        </label>
                        <select name="settings[default_invoice_tax_rate]" id="settings[default_invoice_tax_rate]"
                                class="form-control simple-select">
                            <option value=""><?php _trans('none'); ?></option>
                            <?php foreach ($tax_rates as $tax_rate) { ?>
                                <option value="<?php echo $tax_rate->tax_rate_id; ?>"
                                    <?php check_select(get_setting('default_invoice_tax_rate'), $tax_rate->tax_rate_id); ?>>
                                    <?php echo $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="settings[default_include_item_tax]">
                            <?php _trans('default_invoice_tax_rate_placement'); ?>
                        </label>
                        <select name="settings[default_include_item_tax]" id="settings[default_include_item_tax]"
                                class="form-control simple-select">
                            <option value=""><?php _trans('none'); ?></option>
                            <option value="0" <?php check_select(get_setting('default_include_item_tax'), '0'); ?>>
                                <?php _trans('apply_before_item_tax'); ?>
                            </option>
                            <option value="1" <?php check_select(get_setting('default_include_item_tax'), '1'); ?>>
                                <?php _trans('apply_after_item_tax'); ?>
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="settings[default_item_tax_rate]">
                            <?php _trans('default_item_tax_rate'); ?>
                        </label>
                        <select name="settings[default_item_tax_rate]" id="settings[default_item_tax_rate]"
                                class="form-control simple-select">
                            <option value=""><?php _trans('none'); ?></option>
                            <?php foreach ($tax_rates as $tax_rate) { ?>
                                <option value="<?php echo $tax_rate->tax_rate_id; ?>"
                                    <?php check_select(get_setting('default_item_tax_rate'), $tax_rate->tax_rate_id); ?>>
                                    <?php echo $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <?php _trans('invoice_calculation'); ?>
        </div>
        <div class="panel-body">

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[default_item_tax_rate]">
                            <?php _trans('item'); ?>
                        </label>
                        <ul class="non_sortable">
                            <li class="calculation-state-default calculation-state-default-disabled"><?php _trans('subtotal'); ?></li>
                        </ul>
                        <ul id="sortable1">
                            <?php if(get_setting('item_tax_order') === "1") { ?>
                                <li class="calculation-state-default"><?php _trans('tax'); ?>
                                    <input class="currentposition_item" type="hidden" id="settings[item_tax_order]" name="settings[item_tax_order]" value="<?php echo get_setting('item_tax_order'); ?>">
                                </li>
                                <li class="calculation-state-default"><?php _trans('discount'); ?>
                                    <input class="currentposition_item" type="hidden" id="settings[item_discount_order]" name="settings[item_discount_order]" value="<?php echo get_setting('item_discount_order'); ?>">
                                </li>
                            <?php } else { ?>
                                <li class="calculation-state-default"><?php _trans('discount'); ?>
                                    <input class="currentposition_item" type="hidden" id="settings[item_discount_order]" name="settings[item_discount_order]" value="<?php echo get_setting('item_discount_order'); ?>">
                                </li>
                                <li class="calculation-state-default"><?php _trans('tax'); ?>
                                    <input class="currentposition_item" type="hidden" id="settings[item_tax_order]" name="settings[item_tax_order]" value="<?php echo get_setting('item_tax_order'); ?>">
                                </li>
                            <?php } ?>
                        </ul>
                        <ul class="non_sortable">
                            <li class="calculation-state-default calculation-state-default-disabled"><?php _trans('total'); ?></li>
                        </ul>
                    </div>
                </div>

                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[default_item_tax_rate]">
                            <?php _trans('invoice'); ?>
                        </label>
                        <ul id="sortable2">
                            <?php if(get_setting('invoice_tax_order') === "1") { ?>
                                <li class="calculation-state-default"><?php _trans('tax'); ?>
                                    <input class="currentposition_invoice" type="hidden" id="settings[invoice_tax_order]" name="settings[invoice_tax_order]" value="<?php echo get_setting('invoice_tax_order'); ?>">
                                </li>
                                <li class="calculation-state-default"><?php _trans('discount'); ?>
                                    <input class="currentposition_invoice" type="hidden" id="settings[invoice_discount_order]" name="settings[invoice_discount_order]" value="<?php echo get_setting('invoice_discount_order'); ?>">
                                </li>
                            <?php } else { ?>
                                <li class="calculation-state-default"><?php _trans('discount'); ?>
                                    <input class="currentposition_invoice" type="hidden" id="settings[invoice_discount_order]" name="settings[invoice_discount_order]" value="<?php echo get_setting('invoice_discount_order'); ?>">
                                </li>
                                <li class="calculation-state-default"><?php _trans('tax'); ?>
                                    <input class="currentposition_invoice" type="hidden" id="settings[invoice_tax_order]" name="settings[invoice_tax_order]" value="<?php echo get_setting('invoice_tax_order'); ?>">
                                </li>
                            <?php } ?>
                        </ul>
                        <ul class="non_sortable">
                            <li class="calculation-state-default calculation-state-default-disabled"><?php _trans('total'); ?></li>
                        </ul>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>


<script>
    $( function() {
        $( "#sortable1" ).sortable({
            stop: function () {
                var cnt = 1;
                $('input.currentposition_item').each(function() {
                    $(this).val(cnt);
                    cnt++;
                });
            }
        });

        $( "#sortable2" ).sortable({
            stop: function () {
                var cnt = 1;
                $('input.currentposition_invoice').each(function() {
                    $(this).val(cnt);
                    cnt++;
                });
            }
        });

        $( "#sortable1 li, #sortable2 li" ).disableSelection();
    } );
</script>


<!-- Move it to the CSS file -->
<style>
    .non_sortable, #sortable1, #sortable2 { list-style-type: none; margin: 0; padding: 0; zoom: 1; }
    .non_sortable li, #sortable1 li, #sortable2 li { margin: 0 5px 5px 5px; padding: 3px; width: 90%; }
    .calculation-state-default{ border: 1px solid #c5c5c5; background: #f6f6f6; font-weight: normal; color: #454545; }
    .calculation-state-default-disabled{ opacity: .35; filter: Alpha(Opacity=35); background-image: none; }
</style>