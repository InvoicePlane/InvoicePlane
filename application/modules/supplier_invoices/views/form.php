<div id="headerbar">
    <h1 class="headerbar-title">Supplier invoice</h1>
</div>

<div id="content">
    <?php $this->layout->load_view('layout/alerts'); ?>
    <?php if ( ! empty($form_errors)) : ?><div class="alert alert-danger"><ul><?php foreach ($form_errors as $error) : ?><li><?php _htmlsc($error); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post" class="form-horizontal">
        <?php _csrf_field(); ?>
        <div class="form-group"><label class="col-sm-3 control-label">Supplier</label><div class="col-sm-6"><select name="supplier_id" class="form-control" required><option value="">Select a supplier</option><?php foreach ($suppliers as $supplier) : ?><option value="<?php echo (int) $supplier['supplier_id']; ?>" <?php echo (int) ($invoice['supplier_id'] ?? 0) === (int) $supplier['supplier_id'] ? 'selected' : ''; ?>><?php _htmlsc($supplier['supplier_name']); ?></option><?php endforeach; ?></select></div></div>
        <div class="form-group"><label class="col-sm-3 control-label">Invoice number</label><div class="col-sm-6"><input class="form-control" name="supplier_invoice_number" value="<?php _htmlsc($invoice['supplier_invoice_number'] ?? ''); ?>" required></div></div>
        <div class="form-group"><label class="col-sm-3 control-label">External reference</label><div class="col-sm-6"><input class="form-control" name="external_reference" value="<?php _htmlsc($invoice['external_reference'] ?? ''); ?>"></div></div>
        <div class="form-group"><label class="col-sm-3 control-label">Invoice date</label><div class="col-sm-3"><input type="date" class="form-control" name="supplier_invoice_date" value="<?php _htmlsc($invoice['supplier_invoice_date'] ?? ''); ?>" required></div><label class="col-sm-1 control-label">Due</label><div class="col-sm-3"><input type="date" class="form-control" name="supplier_due_date" value="<?php _htmlsc($invoice['supplier_due_date'] ?? ''); ?>"></div></div>
        <div class="form-group"><label class="col-sm-3 control-label">Currency</label><div class="col-sm-2"><input class="form-control" name="currency_code" maxlength="3" value="<?php _htmlsc($invoice['currency_code'] ?? 'EUR'); ?>"></div></div>
        <div class="form-group"><label class="col-sm-3 control-label">Subtotal</label><div class="col-sm-3"><input class="form-control" name="subtotal" value="<?php _htmlsc($invoice['subtotal'] ?? ''); ?>"></div></div>
        <div class="form-group"><label class="col-sm-3 control-label">Tax total</label><div class="col-sm-3"><input class="form-control" name="tax_total" value="<?php _htmlsc($invoice['tax_total'] ?? ''); ?>"></div></div>
        <div class="form-group"><label class="col-sm-3 control-label">Total</label><div class="col-sm-3"><input class="form-control" name="total" value="<?php _htmlsc($invoice['total'] ?? ''); ?>"></div></div>
        <div class="form-group"><label class="col-sm-3 control-label">Notes</label><div class="col-sm-6"><textarea class="form-control" name="notes"><?php _htmlsc($invoice['notes'] ?? ''); ?></textarea></div></div>

        <h3>Items</h3>
        <?php $formItems = $items ?: [[]]; for ($row = 0; $row < max(3, count($formItems)); $row++) : $item = $formItems[$row] ?? []; ?>
            <div class="form-inline" style="margin-bottom: 8px;"><input class="form-control" name="item_name[]" placeholder="Description" value="<?php _htmlsc($item['item_name'] ?? ''); ?>"><input class="form-control" name="quantity[]" type="number" step="0.000001" placeholder="Qty" value="<?php _htmlsc($item['quantity'] ?? '1'); ?>"><input class="form-control" name="unit_price[]" placeholder="Unit price" value="<?php _htmlsc($item['unit_price'] ?? ''); ?>"><input class="form-control" name="tax_rate[]" placeholder="Tax %" value="<?php _htmlsc($item['tax_rate'] ?? ''); ?>"><input class="form-control" name="item_total[]" placeholder="Total" value="<?php _htmlsc($item['total'] ?? ''); ?>"></div>
        <?php endfor; ?>
        <button class="btn btn-primary" type="submit" name="btn_submit" value="1">Save</button>
        <button class="btn btn-default" type="submit" name="btn_cancel" value="1">Cancel</button>
    </form>
</div>
