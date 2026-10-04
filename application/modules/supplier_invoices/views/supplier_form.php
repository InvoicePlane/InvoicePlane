<div id="headerbar"><h1 class="headerbar-title">Supplier</h1></div>
<div id="content">
    <?php if ( ! empty($form_errors)) : ?><div class="alert alert-danger"><ul><?php foreach ($form_errors as $error) : ?><li><?php _htmlsc($error); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post" class="form-horizontal">
        <?php _csrf_field(); ?>
        <?php foreach ([['supplier_name', 'Name'], ['supplier_company', 'Company'], ['supplier_vat_id', 'VAT ID'], ['supplier_tax_code', 'Tax code'], ['supplier_peppol_id', 'Peppol ID'], ['supplier_email', 'Email'], ['supplier_address_1', 'Address'], ['supplier_address_2', 'Address 2'], ['supplier_city', 'City'], ['supplier_state', 'State'], ['supplier_zip', 'ZIP'], ['supplier_country', 'Country']] as $field) : ?><div class="form-group"><label class="col-sm-3 control-label"><?php _htmlsc($field[1]); ?></label><div class="col-sm-6"><input class="form-control" name="<?php echo $field[0]; ?>" value="<?php _htmlsc($supplier[$field[0]] ?? ''); ?>" <?php echo $field[0] === 'supplier_name' ? 'required' : ''; ?>></div></div><?php endforeach; ?>
        <div class="form-group"><div class="col-sm-offset-3 col-sm-6"><label><input type="checkbox" name="supplier_active" value="1" <?php echo ! isset($supplier['supplier_active']) || (int) $supplier['supplier_active'] === 1 ? 'checked' : ''; ?>> Active</label></div></div>
        <button class="btn btn-primary" type="submit" name="btn_submit" value="1">Save</button>
        <button class="btn btn-default" type="submit" name="btn_cancel" value="1">Cancel</button>
    </form>
</div>
