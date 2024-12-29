<div class="form-group">
    <label for="tags_invoice"><?php _trans('invoices'); ?></label>
    <select id="tags_invoice" class="tag-select form-control">
        <option value="{{{invoice_number}}}">
            <?php _trans('id'); ?>
        </option>
        <option value="{{{invoice_status}}}">
            <?php _trans('status'); ?>
        </option>
        <optgroup label="<?php _trans('invoice_dates'); ?>">
            <option value="{{{invoice_date_due}}}">
                <?php _trans('due_date'); ?>
            </option>
            <option value="{{{invoice_date_created}}}">
                <?php _trans('invoice_date'); ?>
            </option>
        </optgroup>
        <optgroup label="<?php _trans('invoice_amounts'); ?>">
            <option value="{{{invoice_item_subtotal}}}">
                <?php _trans('subtotal'); ?>
            </option>
            <option value="{{{invoice_item_tax_total}}}">
                <?php _trans('invoice_tax'); ?>
            </option>
            <option value="{{{invoice_total}}}">
                <?php _trans('total'); ?>
            </option>
            <option value="{{{invoice_paid}}}">
                <?php _trans('total_paid'); ?>
            </option>
            <option value="{{{invoice_balance}}}">
                <?php _trans('balance'); ?>
            </option>
        </optgroup>
        <optgroup label="<?php _trans('extra_information'); ?>">
            <option value="{{{invoice_terms}}}">
                <?php _trans('invoice_terms'); ?>
            </option>
        <option value="{{{invoice_guest_url}}}">
            <?php _trans('guest_url'); ?>
        </option>
<!--                 <option value="{{{payment_method}}}"> -->
<!--                     <?php _trans('payment_method'); ?> -->
<!--                 </option> -->
        </optgroup>

        <optgroup label="<?php _trans('custom_fields'); ?>">
            if(isset($custom_fields['ip_invoice_custom'])) {
                <?php foreach ($custom_fields['ip_invoice_custom'] as $custom) { ?>
                    <option value="{{{<?php echo 'ip_cf_' . $custom->custom_field_id; ?>}}}">
                        <?php echo $custom->custom_field_label . ' (ID ' . $custom->custom_field_id . ')'; ?>
                    </option>
                <?php } ?>
            <?php } ?>
        </optgroup>
    </select>
</div>
