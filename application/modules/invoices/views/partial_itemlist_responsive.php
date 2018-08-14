<?php
    $invoice_disabled = "";
    if ($invoice->is_read_only == 1) $invoice_disabled = ' disabled="disabled"';
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
                                <input type="text" name="item_name" class="input-sm form-control" value="">
                            </div>
                            <?php if ($invoice->sumex_id == ""): ?>
                                <div class="input-group">
                                    <label for="item_description" class="input-group-addon ig-addon-aligned"><?php _trans('description'); ?></label>
                                    <textarea name="item_description" class="input-sm form-control h135rem"></textarea>
                                </div>
                            <?php else: ?>
                                <div class="input-group">
                                    <label for="item_date" class="input-group-addon ig-addon-aligned"><?php _trans('date'); ?></label>
                                    <input type="text" name="item_date" class="input-sm form-control datepicker"
                                           value="<?php echo format_date(@$item->item_date); ?>"<?=$invoice_disabled;?>>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-5 col-md-6 col-lg-7">
                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <div class="input-group">
                                <label for="item_quantity" class="input-group-addon ig-addon-aligned">
                                    <?php _trans('quantity'); ?></label>
                                <input type="text" name="item_quantity" class="input-sm form-control" value="">
                            </div>
                            <div class="input-group">
                                <label for="item_product_unit_id" class="input-group-addon ig-addon-aligned">
                                    <?php _trans('product_unit'); ?></label>
                                <select name="item_product_unit_id" class="form-control input-sm">
                                    <option value="0"><?php _trans('none'); ?></option>
                                    <?php foreach ($units as $unit) { ?>
                                        <option value="<?php echo $unit->unit_id; ?>">
                                            <?php echo $unit->unit_name . "/" . $unit->unit_name_plrl; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-xs-9">
                                    <div class="input-group">
                                        <label for="item_price" class="input-group-addon ig-addon-aligned">
                                            <?php _trans('price'); ?></label>
                                        <input type="text" name="item_price" class="input-sm form-control" value="">
                                        <div class="input-group-addon">
                                            <?php echo get_setting('currency_symbol'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-3">
                                    <div class="input-group">
                                        <select name="item_price_isgross"
                                                aria-label="<?php _trans('price_type'); ?>"
                                                class="input-sm form-control">
                                             <option value="0"><?php _trans('price_is_net'); ?></option>
                                             <option value="1" <?php check_select(get_setting('default_item_price_type'), 1); ?>>
                                                 <?php _trans('price_is_gross'); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group">
                                <label for="item_discount_amount" class="input-group-addon ig-addon-aligned">
                                    <?php _trans('item_discount'); ?></label>
                                <input type="text" name="item_discount_amount"
                                        class="input-sm form-control"
                                        value="" data-toggle="tooltip" data-placement="bottom"
                                        title="<?php echo get_setting('currency_symbol') . ' ' . trans('per_item'); ?>">
                                <div class="input-group-addon">
                                    <?php echo get_setting('currency_symbol'); ?>
                                </div>
                            </div>
                            <div class="input-group">
                                <label for="item_tax_rate_id" class="input-group-addon ig-addon-aligned">
                                    <?php _trans('tax_rate'); ?></label>
                                <select name="item_tax_rate_id" class="form-control input-sm">
                                    <option value="0"><?php _trans('none'); ?></option>
                                    <?php foreach ($tax_rates as $tax_rate) { ?>
                                        <option value="<?php echo $tax_rate->tax_rate_id; ?>"
                                            <?php check_select(get_setting('default_item_tax_rate'), $tax_rate->tax_rate_id); ?>>
                                            <?php echo format_amount($tax_rate->tax_rate_percent) . '% - ' . $tax_rate->tax_rate_name; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
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

                            <?php if (get_setting('default_item_tax_after_discount') == '1') { ?>
                                <div class="invisible">
                                    <div class="row mb-1">
                                        <div class="col-xs-9 col-sm-8">
                                            <?php _trans('discount'); ?>:
                                        </div>
                                        <div class="col-xs-3 col-sm-4">
                                            <span name="item_discount_total"></span>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-xs-9 col-sm-8">
                                            <?php _trans('subtotal_after_discount'); ?>:
                                        </div>
                                        <div class="col-xs-3 col-sm-4">
                                            <span name="subtotal_recalc"></span>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="row mb-1">
                                <div class="col-xs-9 col-sm-8">
                                    <?php _trans('tax'); ?>:
                                </div>
                                <div class="col-xs-3 col-sm-4">
                                    <span name="item_tax_total"></span>
                                </div>
                            </div>

                            <?php if (get_setting('default_item_tax_after_discount') != '1') { ?>
                                <div class="invisible">
                                    <div class="row mb-1">
                                        <div class="col-xs-9 col-sm-8">
                                            <?php _trans('subtotal_after_discount'); ?>:
                                        </div>
                                        <div class="col-xs-3 col-sm-4">
                                            <span name="subtotal_recalc"></span>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-xs-9 col-sm-8">
                                            <?php _trans('discount'); ?>:
                                        </div>
                                        <div class="col-xs-3 col-sm-4">
                                            <span name="item_discount_total"></span>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

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
        <div class="item form-group details-box">
            <div class="row">
                <div class="col-xs-12 col-sm-7 col-md-6 col-lg-5">
                    <div class="row">
                        <div class="col-xs-12 col-sm-1">
                            <button type="button" class="btn btn-link up"
                                    title="<?php _trans('move_up'); ?>"<?=$invoice_disabled;?>>
                                <i class="fa fa-chevron-up"></i>
                            </button>
                            <button type="button" class="btn btn-link down"
                                    title="<?php _trans('move_down'); ?>"<?=$invoice_disabled;?>>
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
                                    class="js-item-recurrence-toggler cursor-pointer fa <?php echo $item_recurrence_class ?>">
                                </i>
                                <input type="hidden" name="item_is_recurring" value="<?php echo $item_recurrence_state ?>"/>
                            <?php endif; ?>
                            <?php if ($invoice->is_read_only != 1): ?>
                                <button type="button" class="btn_delete_item btn btn-link"
                                        title="<?php _trans('delete'); ?>" data-item-id="<?php echo $item->item_id; ?>">
                                    <i class="fa fa-trash-o text-danger"></i>
                                </button>
                            <?php endif; ?>
                        </div>

                        <div class="col-xs-12 col-sm-11">
                            <input type="hidden" name="invoice_id" value="<?=$invoice_id;?>">
                            <input type="hidden" name="item_id" value="<?=$item->item_id;?>"<?=$invoice_disabled;?>>
                            <input type="hidden" name="item_task_id" class="item-task-id"
                                    value="<?php if ($item->item_task_id) echo $item->item_task_id; ?>">
                            <input type="hidden" name="item_product_id" value="<?=$item->item_product_id;?>">

                            <div class="input-group">
                                <label for="item_name" class="input-group-addon ig-addon-aligned">
                                    <?php _trans('item'); ?></label>
                                <input type="text" name="item_name" class="input-sm form-control"
                                        value="<?=_htmlsc($item->item_name);?>"<?=$invoice_disabled;?>>
                            </div>
                            <?php if ($invoice->sumex_id == "") : ?>
                                <div class="input-group">
                                    <label for="item_description" class="input-group-addon ig-addon-aligned">
                                        <?php _trans('description'); ?></label>
                                    <textarea name="item_description" class="input-sm form-control h135rem"
                                        <?=$invoice_disabled;?>><?=htmlsc($item->item_description);?></textarea>
                                </div>
                            <?php else: ?>
                                <div class="input-group">
                                    <label for="item_date" class="input-group-addon ig-addon-aligned">
                                        <?php _trans('date'); ?></label>
                                    <input type="text" name="item_date" class="input-sm form-control datepicker"
                                            value="<?php echo format_date($item->item_date);?>"<?=$invoice_disabled;?>>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-5 col-md-6 col-lg-7">
                    <div class="row">
                        <div class="col-xs-12 col-lg-6">
                            <div class="input-group">
                                <label for="item_quantity" class="input-group-addon ig-addon-aligned">
                                    <?php _trans('quantity'); ?></label>
                                <input type="text" name="item_quantity"
                                        class="input-sm form-control"
                                        value="<?=format_amount($item->item_quantity);?>"<?=$invoice_disabled;?>>
                            </div>
                            <div class="input-group">
                                <label for="item_product_unit_id" class="input-group-addon ig-addon-aligned">
                                    <?php _trans('product_unit'); ?></label>
                                <select name="item_product_unit_id" class="form-control input-sm"<?=$invoice_disabled;?>>
                                    <option value="0"><?php _trans('none'); ?></option>
                                    <?php foreach ($units as $unit) { ?>
                                        <option value="<?php echo $unit->unit_id; ?>"
                                            <?php check_select($item->item_product_unit_id, $unit->unit_id); ?>>
                                            <?php echo htmlsc($unit->unit_name) . "/" . htmlsc($unit->unit_name_plrl); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-xs-8">
                                    <div class="input-group">
                                        <label for="item_price" class="input-group-addon ig-addon-aligned">
                                            <?php _trans('price'); ?></label>
                                        <input type="text" name="item_price"
                                                class="input-sm form-control"
                                                value="<?php echo format_amount($item->item_price); ?>"<?=$invoice_disabled;?>>
                                        <div class="input-group-addon"><?php echo get_setting('currency_symbol'); ?></div>
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="input-group">
                                        <select name="item_price_isgross" aria-label="<?php _trans('price_type'); ?>"
                                                class="input-sm form-control"<?=$invoice_disabled;?>>
                                             <option value="0"><?php _trans('price_is_net'); ?></option>
                                             <option value="1" <?php check_select($item->item_price_isgross, 1); ?>>
                                                 <?php _trans('price_is_gross'); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group">
                                <label for="item_discount_amount" class="input-group-addon ig-addon-aligned">
                                    <?php _trans('item_discount'); ?></label>
                                <input type="text" name="item_discount_amount"
                                       class="input-sm form-control"
                                       value="<?php echo format_amount($item->item_discount_amount); ?>"
                                       data-toggle="tooltip" data-placement="bottom"
                                       title="<?php echo get_setting('currency_symbol') . ' ' . trans('per_item'); ?>"<?=$invoice_disabled;?>>
                                <div class="input-group-addon">
                                    <?php echo get_setting('currency_symbol'); ?>
                                </div>
                            </div>
                            <div class="input-group">
                                <label for="item_tax_rate_id" class="input-group-addon ig-addon-aligned">
                                    <?php _trans('tax_rate'); ?></label>
                                <select name="item_tax_rate_id" class="form-control input-sm"<?=$invoice_disabled;?>>
                                    <option value="0"><?php _trans('none'); ?></option>
                                    <?php foreach ($tax_rates as $tax_rate) { ?>
                                        <option value="<?php echo $tax_rate->tax_rate_id; ?>"
                                            <?php check_select($item->item_tax_rate_id, $tax_rate->tax_rate_id); ?>>
                                            <?php echo format_amount($tax_rate->tax_rate_percent) . '% - ' . $tax_rate->tax_rate_name; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <?php if (get_setting('default_item_tax_after_discount') == 1) {
                            $this->layout->load_view('invoices/partial_itemlist_calc_posttax', array('item' => $item));
                          } else {
                            $this->layout->load_view('invoices/partial_itemlist_calc_pretax', array('item' => $item));
                          }
                        ?>

                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    </div>
</div>

<br/>

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
            <tr>
                <td style="width: 40%;"><?php _trans('subtotal'); ?></td>
                <td style="width: 60%;"
                    class="amount"><?php echo format_currency($invoice->invoice_item_subtotal); ?></td>
            </tr>
                        <tr>
                <td><?php _trans('item_tax'); ?></td>
                <td class="amount"><?php echo format_currency($invoice->invoice_item_tax_total); ?></td>
            </tr>
                        <?php if ($invoice_tax_rates) { ?>
            <tr>
                <td><?php _trans('invoice_tax'); ?></td>
                <td>
                  <?php foreach ($invoice_tax_rates as $invoice_tax_rate) { ?>
                            <form method="post"
                                action="<?php echo site_url('invoices/delete_invoice_tax/' . $invoice->invoice_id . '/' . $invoice_tax_rate->invoice_tax_rate_id) ?>">
                                <?php _csrf_field(); ?>
                                <span class="amount">
                                    <?php echo format_currency($invoice_tax_rate->invoice_tax_rate_amount); ?>
                                </span>
                                <span class="text-muted">
                                    <?php echo htmlsc($invoice_tax_rate->invoice_tax_rate_name) . ' ' . format_amount($invoice_tax_rate->invoice_tax_rate_percent) ?>
                                </span>
                                <button type="submit" class="btn btn-xs btn-link"
                                        onclick="return confirm('<?php _trans('delete_tax_warning'); ?>');">
                                    <i class="fa fa-trash-o"></i>
                                </button>
                            </form>
                    <?php } ?>
                </td>
            </tr>
                        <?php } ?>
            <tr>
                <td class="td-vert-middle"><?php _trans('discount'); ?></td>
                <td class="clearfix">
                    <div class="discount-field">
                        <div class="input-group input-group-sm">
                            <input id="invoice_discount_amount" name="invoice_discount_amount"
                                   class="discount-option form-control input-sm amount"
                                   value="<?php echo format_amount($invoice->invoice_discount_amount != 0 ? $invoice->invoice_discount_amount : ''); ?>"<?=$invoice_disabled;?>>
                            <div class="input-group-addon"><?php echo get_setting('currency_symbol'); ?></div>
                        </div>
                    </div>
                    <div class="discount-field">
                        <div class="input-group input-group-sm">
                            <input id="invoice_discount_percent" name="invoice_discount_percent"
                                   class="discount-option form-control input-sm amount"
                                                                     value="<?php echo format_amount($invoice->invoice_discount_percent != 0 ? $invoice->invoice_discount_percent : ''); ?>"<?=$invoice_disabled;?>>
                            <div class="input-group-addon">&percnt;</div>
                        </div>
                    </div>
                </td>
            </tr>
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
