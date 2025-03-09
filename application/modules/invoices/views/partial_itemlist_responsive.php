<?php
$invoice_disabled = $invoice->is_read_only != 1 ? '' : ' disabled="disabled"';
?>

<div class="row">
    <div id="item_table" class="items table col-xs-12">
        <div id="new_row" class="form-group details-box" style="display: none;">
            <div class="row">
                <div class="col-xs-12 col-sm-7 col-md-6 col-lg-5">
                    <div class="row">
                        <div class="col-xs-12 col-sm-1">
                            <button type="button" class="btn btn-link up" title="<?php _trans('move_up'); ?>">
                                <i class="fa fa-chevron-up"></i>
                            </button>
                            <button type="button" class="btn btn-link down" title="<?php _trans('move_down'); ?>">
                                <i class="fa fa-chevron-down"></i>
                            </button>
                            <?php if ($invoice->invoice_is_recurring) : ?>
                                <i title="<?php echo trans('recurring') ?>" class="js-item-recurrence-toggler cursor-pointer fa fa-calendar-o text-muted"></i>
                                <input type="hidden" name="item_is_recurring" value=""/>
                            <?php endif; ?>
                            <button type="button" class="btn_delete_item btn btn-link btn-sm" title="<?php _trans('delete'); ?>">
                                <i class="fa fa-trash-o text-danger"></i>
                            </button>
                        </div>

                        <div class="col-xs-12 col-sm-11">
                            <div class="input-group">
                                <label for="item_name" class="input-group-addon ig-addon-aligned"><?php _trans('item'); ?></label>
                                <input type="text" name="item_name" id="item_name" class="form-control" value="">
                            </div>
                            <?php if ($invoice->sumex_id == ""): ?>
                                <div class="input-group">
                                    <label for="item_description" class="input-group-addon ig-addon-aligned"><?php _trans('description'); ?></label>
                                    <textarea name="item_description" id="item_description" class="form-control"></textarea>
                                </div>
                            <?php else: ?>
                                <div class="input-group">
                                    <label for="item_date" class="input-group-addon ig-addon-aligned"><?php _trans('date'); ?></label>
                                    <input type="text" name="item_date" id="item_date" class="form-control datepicker"
                                           value="<?php echo format_date(@$item->item_date); ?>"<?php echo $invoice_disabled; ?>>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-5 col-md-6 col-lg-7">
                    <div class="row">
                        <div class="col-xs-12 col-lg-6">
                            <div class="input-group">
                                <label for="item_quantity" class="input-group-addon ig-addon-aligned"><?php _trans('quantity'); ?></label>
                                <input type="text" name="item_quantity" id="item_quantity" class="form-control" value="">
                            </div>
                            <div class="input-group">
                                <label for="item_product_unit_id" class="input-group-addon ig-addon-aligned"><?php _trans('product_unit'); ?></label>
                                <select name="item_product_unit_id" id="item_product_unit_id" class="form-control">
                                    <option value="0"><?php _trans('none'); ?></option>
                                    <?php foreach ($units as $unit) { ?>
                                        <option value="<?php echo $unit->unit_id; ?>">
                                            <?php echo $unit->unit_name . "/" . $unit->unit_name_plrl; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="item_price" class="input-group-addon ig-addon-aligned"><?php _trans('price'); ?></label>
                                <input type="text" name="item_price" id="item_price" class="form-control" value="">
                                <div class="input-group-addon"><?php echo get_setting('currency_symbol'); ?></div>
                            </div>
<?php
                            if ( ! $legacy_calculation)
                            {
                                $this->layout->load_view('layout/partial/itemlist_responsive_item_discount_input');
                            }
?>
                            <div class="input-group">
                                <label for="item_tax_rate_id" class="input-group-addon ig-addon-aligned"><?php _trans('tax_rate'); ?></label>
                                <select name="item_tax_rate_id" id="item_tax_rate_id" class="form-control">
                                    <option value="0"><?php _trans('none'); ?></option>
                                    <?php foreach ($tax_rates as $tax_rate) { ?>
                                        <option value="<?php echo $tax_rate->tax_rate_id; ?>"
                                            <?php check_select(get_setting('default_item_tax_rate'), $tax_rate->tax_rate_id); ?>>
                                            <?php echo format_amount($tax_rate->tax_rate_percent) . '% - ' . $tax_rate->tax_rate_name; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
<?php
                            if ($legacy_calculation)
                            {
                                $this->layout->load_view('layout/partial/itemlist_responsive_item_discount_input');
                            }
?>
                        </div>

                        <input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>">
                        <input type="hidden" name="item_id" value="">
                        <input type="hidden" name="item_product_id" value="">
                        <input type="hidden" name="item_task_id" class="item-task-id" value="">

                        <div class="col-xs-12 col-md-6 text-right">
                            <div class="row mb-1">
                                <div class="col-xs-9 col-sm-8">
                                    <?php _trans('subtotal'); ?>:
                                </div>
                                <div class="col-xs-3 col-sm-4">
                                    <span name="subtotal"></span>
                                </div>
                            </div>
<?php
                            if ( ! $legacy_calculation)
                            {
                                $this->layout->load_view('layout/partial/itemlist_responsive_item_discount_show');
                            }
?>
                            <div class="row mb-1">
                                <div class="col-xs-9 col-sm-8">
                                    <?php _trans('tax'); ?>:
                                </div>
                                <div class="col-xs-3 col-sm-4">
                                    <span name="item_tax_total"></span>
                                </div>
                            </div>
<?php
                            if ($legacy_calculation)
                            {
                                $this->layout->load_view('layout/partial/itemlist_responsive_item_discount_show');
                            }
?>
                            <div class="row mb-1">
                                <strong>
                                    <div class="col-xs-9 col-sm-8">
                                        <?php _trans('total'); ?>:
                                    </div>
                                    <div class="col-xs-3 col-sm-4">
                                        <span name="item_total"></span>
                                    </div>
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php foreach ($items as $item) { ?>
            <div class="form-group details-box item">
                <div class="row">
                    <div class="col-xs-12 col-sm-7 col-md-6 col-lg-5">
                        <div class="row">
                            <div class="col-xs-12 col-sm-1">
                                <button type="button" class="btn btn-link up" title="<?php _trans('move_up'); ?>"<?php echo $invoice_disabled; ?>>
                                    <i class="fa fa-chevron-up"></i>
                                </button>
                                <button type="button" class="btn btn-link down" title="<?php _trans('move_down'); ?>"<?php echo $invoice_disabled; ?>>
                                    <i class="fa fa-chevron-down"></i>
                                </button>
                                <?php if ($invoice->invoice_is_recurring) :
                                    if ($item->item_is_recurring == 1 || is_null($item->item_is_recurring)) {
                                        $item_recurrence_state = '1';
                                        $item_recurrence_class = 'fa-calendar-check-o text-success';
                                    } else {
                                        $item_recurrence_state = '0';
                                        $item_recurrence_class = 'fa-calendar-o text-muted';
                                    }
                                    ?>
                                    <i title="<?php echo trans('recurring') ?>"
                                        class="js-item-recurrence-toggler cursor-pointer fa <?php echo $item_recurrence_class; ?>">
                                    </i>
                                    <input type="hidden" name="item_is_recurring" value="<?php echo $item_recurrence_state; ?>"/>
                                <?php endif; ?>
                                <?php if ($invoice->is_read_only != 1): ?>
                                    <button type="button" class="btn_delete_item btn btn-link" title="<?php _trans('delete'); ?>" data-item-id="<?php echo $item->item_id; ?>">
                                        <i class="fa fa-trash-o text-danger"></i>
                                    </button>
                                <?php endif; ?>
                            </div>

                            <div class="col-xs-12 col-sm-11">
                                <input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>">
                                <input type="hidden" name="item_id" value="<?php echo $item->item_id; ?>"<?php echo $invoice_disabled; ?>>
                                <input type="hidden" name="item_task_id" class="item-task-id" value="<?php if ($item->item_task_id) echo $item->item_task_id; ?>">
                                <input type="hidden" name="item_product_id" value="<?php echo $item->item_product_id; ?>">

                                <div class="input-group">
                                    <label for="item_name_<?php echo $item->item_id; ?>" class="input-group-addon ig-addon-aligned"><?php _trans('item'); ?></label>
                                    <input type="text" name="item_name" id="item_name_<?php echo $item->item_id; ?>" class="form-control" value="<?php echo _htmlsc($item->item_name); ?>"<?php echo $invoice_disabled; ?>>
                                </div>
                                <?php if ($invoice->sumex_id == "") : ?>
                                    <div class="input-group">
                                        <label for="item_description_<?php echo $item->item_id; ?>" class="input-group-addon ig-addon-aligned"><?php _trans('description'); ?></label>
                                        <textarea name="item_description" id="item_description_<?php echo $item->item_id; ?>" class="form-control"<?php echo $invoice_disabled; ?>><?php echo htmlsc($item->item_description); ?></textarea>
                                    </div>
                                <?php else: ?>
                                    <div class="input-group">
                                        <label for="item_date_<?php echo $item->item_id; ?>" class="input-group-addon ig-addon-aligned"><?php _trans('date'); ?></label>
                                        <input type="text" name="item_date" id="item_date_<?php echo $item->item_id; ?>" class="form-control datepicker" value="<?php echo format_date($item->item_date); ?>"<?php echo $invoice_disabled; ?>>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-5 col-md-6 col-lg-7">
                        <div class="row">
                            <div class="col-xs-12 col-lg-6">
                                <div class="input-group">
                                    <label for="item_quantity_<?php echo $item->item_id; ?>" class="input-group-addon ig-addon-aligned"><?php _trans('quantity'); ?></label>
                                    <input type="text" name="item_quantity" id="item_quantity_<?php echo $item->item_id; ?>" class="form-control" value="<?php echo format_quantity($item->item_quantity); ?>"<?php echo $invoice_disabled; ?>>
                                </div>
                                <div class="input-group">
                                    <label for="item_product_unit_id_<?php echo $item->item_id; ?>" class="input-group-addon ig-addon-aligned"><?php _trans('product_unit'); ?></label>
                                    <select name="item_product_unit_id" id="item_product_unit_id_<?php echo $item->item_id; ?>" class="form-control"<?php echo $invoice_disabled; ?>>
                                        <option value="0"><?php _trans('none'); ?></option>
                                        <?php foreach ($units as $unit) { ?>
                                            <option value="<?php echo $unit->unit_id; ?>"
                                                <?php check_select($item->item_product_unit_id, $unit->unit_id); ?>>
                                                <?php echo htmlsc($unit->unit_name) . "/" . htmlsc($unit->unit_name_plrl); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="input-group">
                                    <label for="item_price_<?php echo $item->item_id; ?>" class="input-group-addon ig-addon-aligned"><?php _trans('price'); ?></label>
                                    <input type="text" name="item_price" id="item_price_<?php echo $item->item_id; ?>" class="form-control"
                                           value="<?php echo format_amount($item->item_price); ?>"<?php echo $invoice_disabled; ?>>
                                    <div class="input-group-addon"><?php echo get_setting('currency_symbol'); ?></div>
                                </div>
<?php
                                if ( ! $legacy_calculation)
                                {
                                    $this->layout->load_view('layout/partial/itemlist_responsive_item_discount_input', ['item' => $item]);
                                }
?>
                                <div class="input-group">
                                    <label for="item_tax_rate_id_<?php echo $item->item_id; ?>" class="input-group-addon ig-addon-aligned"><?php _trans('tax_rate'); ?></label>
                                    <select name="item_tax_rate_id" id="item_tax_rate_id_<?php echo $item->item_id; ?>" class="form-control"<?php echo $invoice_disabled; ?>>
                                        <option value="0"><?php _trans('none'); ?></option>
                                        <?php foreach ($tax_rates as $tax_rate) { ?>
                                            <option value="<?php echo $tax_rate->tax_rate_id; ?>"
                                                <?php check_select($item->item_tax_rate_id, $tax_rate->tax_rate_id); ?>>
                                                <?php echo format_amount($tax_rate->tax_rate_percent) . '% - ' . $tax_rate->tax_rate_name; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
<?php
                                if ($legacy_calculation)
                                {
                                    $this->layout->load_view('layout/partial/itemlist_responsive_item_discount_input', ['item' => $item]);
                                }
?>
                            </div>
                            <div class="col-xs-12 col-md-6 text-right">
                                <div class="row mb-1">
                                    <div class="col-xs-9 col-sm-8">
                                        <?php _trans('subtotal'); ?>:
                                    </div>
                                    <div class="col-xs-3 col-sm-4">
                                        <?php echo format_currency($item->item_subtotal); ?>
                                    </div>
                                </div>
<?php
                                if ( ! $legacy_calculation)
                                {
                                    $this->layout->load_view('layout/partial/itemlist_responsive_item_discount_show', ['item' => $item]);
                                }
?>
                                <div class="row mb-1">
                                    <div class="col-xs-9 col-sm-8">
                                        <?php _trans('tax'); ?>:
                                    </div>
                                    <div class="col-xs-3 col-sm-4">
                                        <?php echo format_currency($item->item_tax_total); ?>
                                    </div>
                                </div>
<?php
                                if ($legacy_calculation)
                                {
                                    $this->layout->load_view('layout/partial/itemlist_responsive_item_discount_show', ['item' => $item]);
                                }
?>
                                <div class="row mb-1">
                                    <div class="col-xs-9 col-sm-8">
                                        <b><?php _trans('total'); ?>:</b>
                                    </div>
                                    <div class="col-xs-3 col-sm-4">
                                        <b><?php echo format_currency($item->item_total); ?></b>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<br>

<div class="row">
    <div class="col-xs-12 col-md-4">
        <div class="btn-group">
            <?php if ($invoice->is_read_only != 1) { ?>
                <a href="javascript:void(0);" class="btn_add_row btn btn-sm btn-default">
                    <i class="fa fa-plus"></i> <?php _trans('add_new_row'); ?>
                </a>
                <a href="javascript:void(0);" class="btn_add_product btn btn-sm btn-default">
                    <i class="fa fa-database"></i>
                    <?php _trans('add_product'); ?>
                </a>
                <a href="javascript:void(0);" class="btn_add_task btn btn-sm btn-default">
                    <i class="fa fa-database"></i> <?php _trans('add_task'); ?>
                </a>
            <?php } ?>
        </div>
    </div>

    <div class="col-xs-12 visible-xs visible-sm"><br></div>

    <div class="col-xs-12 col-md-6 col-md-offset-2 col-lg-4 col-lg-offset-4">
        <table class="table table-bordered text-right">
<?php
            if ( ! $legacy_calculation)
            {
                $this->layout->load_view('invoices/partial_itemlist_table_invoice_discount');
            }
?>
            <tr>
                <td style="width: 40%;"><?php _trans('subtotal'); ?></td>
                <td style="width: 60%;"
                class="amount"><?php echo format_currency($invoice->invoice_item_subtotal); ?></td>
            </tr>
            <tr>
                <td><?php _trans('item_tax'); ?></td>
                <td class="amount"><?php echo format_currency($invoice->invoice_item_tax_total); ?></td>
            </tr>
<?php
            if ($legacy_calculation)
            {
?>
            <tr>
                <td><?php _trans('invoice_tax'); ?></td>
                <td>
                    <?php if ($invoice_tax_rates) {
                        foreach ($invoice_tax_rates as $invoice_tax_rate) { ?>
                            <form method="post"
                                  action="<?php echo site_url('invoices/delete_invoice_tax/' . $invoice->invoice_id . '/' . $invoice_tax_rate->invoice_tax_rate_id) ?>">
                            <?php _csrf_field(); ?>
                            <span class="amount">
                                <?php echo format_currency($invoice_tax_rate->invoice_tax_rate_amount); ?>
                            </span>
                            <span class="text-muted">
                                <?php echo htmlsc($invoice_tax_rate->invoice_tax_rate_name) . ' ' . format_amount($invoice_tax_rate->invoice_tax_rate_percent) ?>
                            </span>
                            <button type="submit" class="btn btn-xs btn-link" onclick="return confirm('<?php _trans('delete_tax_warning'); ?>');">
                                <i class="fa fa-trash-o"></i>
                            </button>
                        </form>
                    <?php }
                        } else {
                            echo format_currency('0');
                        } ?>
                </td>
            </tr>
<?php
                $this->layout->load_view('invoices/partial_itemlist_table_invoice_discount');
            }
?>
            <tr>
                <td><?php _trans('total'); ?></td>
                <td class="amount"><b><?php echo format_currency($invoice->invoice_total); ?></b></td>
            </tr>
            <tr>
                <td><?php _trans('paid'); ?></td>
                <td class="amount"><b><?php echo format_currency($invoice->invoice_paid); ?></b></td>
            </tr>
            <tr>
                <td><b><?php _trans('balance'); ?></b></td>
                <td class="amount"><b><?php echo format_currency($invoice->invoice_balance); ?></b></td>
            </tr>
        </table>
    </div>

</div>
