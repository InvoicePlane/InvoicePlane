<div id="headerbar"><h1 class="headerbar-title"><?php _trans('supplier'); ?></h1></div>
<div id="content">
    <?php if ( ! empty($form_errors)) : ?><div class="alert alert-danger"><ul><?php foreach ($form_errors as $error) : ?><li><?php _htmlsc($error); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post" class="form-horizontal">
        <?php _csrf_field(); ?>
        <?php foreach ([['supplier_name', 'name'], ['supplier_company', 'company'], ['supplier_vat_id', 'vat_id'], ['supplier_tax_code', 'tax_code'], ['supplier_peppol_id', 'peppol_id'], ['supplier_email', 'email'], ['supplier_address_1', 'address'], ['supplier_address_2', 'address_2'], ['supplier_city', 'city'], ['supplier_state', 'state'], ['supplier_zip', 'zip'], ['supplier_country', 'country']] as $field) : ?><div class="form-group"><label class="col-sm-3 control-label"><?php _trans($field[1]); ?></label><div class="col-sm-6"><input class="form-control" name="<?php echo $field[0]; ?>" value="<?php _htmlsc($supplier[$field[0]] ?? ''); ?>" <?php echo $field[0] === 'supplier_name' ? 'required' : ''; ?>></div></div><?php endforeach; ?>
        <div class="form-group"><div class="col-sm-offset-3 col-sm-6"><label><input type="checkbox" name="supplier_active" value="1" <?php echo ! isset($supplier['supplier_active']) || (int) $supplier['supplier_active'] === 1 ? 'checked' : ''; ?>> <?php _trans('active'); ?></label></div></div>
        <button class="btn btn-primary" type="submit" name="btn_submit" value="1"><?php _trans('save'); ?></button>
        <button class="btn btn-default" type="submit" name="btn_cancel" value="1"><?php _trans('cancel'); ?></button>
    </form>
</div>
