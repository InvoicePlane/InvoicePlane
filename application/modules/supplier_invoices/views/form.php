<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('supplier_invoice'); ?></h1>
</div>

<div id="content">
    <?php $this->layout->load_view('layout/alerts'); ?>
    <?php if ( ! empty($form_errors)) : ?><div class="alert alert-danger"><ul><?php foreach ($form_errors as $error) : ?><li><?php _htmlsc($error); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post" class="form-horizontal">
        <?php _csrf_field(); ?>
        <input type="hidden" name="supplier_invoice_id" value="<?php echo (int) ($invoice['supplier_invoice_id'] ?? 0); ?>">
        <div class="form-group"><label class="col-sm-3 control-label"><?php _trans('supplier'); ?></label><div class="col-sm-6"><select name="supplier_id" class="form-control" required><option value=""><?php _trans('select_supplier'); ?></option><?php foreach ($suppliers as $supplier) : ?><option value="<?php echo (int) $supplier['supplier_id']; ?>" <?php echo (int) ($invoice['supplier_id'] ?? 0) === (int) $supplier['supplier_id'] ? 'selected' : ''; ?>><?php _htmlsc($supplier['supplier_name']); ?></option><?php endforeach; ?></select></div></div>
        <div class="form-group"><label class="col-sm-3 control-label"><?php _trans('invoice_number'); ?></label><div class="col-sm-6"><input class="form-control" name="supplier_invoice_number" value="<?php _htmlsc($invoice['supplier_invoice_number'] ?? ''); ?>" required></div></div>
        <div class="form-group"><label class="col-sm-3 control-label"><?php _trans('external_reference'); ?></label><div class="col-sm-6"><input class="form-control" name="external_reference" value="<?php _htmlsc($invoice['external_reference'] ?? ''); ?>"></div></div>
        <div class="form-group"><label class="col-sm-3 control-label"><?php _trans('invoice_date'); ?></label><div class="col-sm-3"><input type="date" class="form-control" name="supplier_invoice_date" value="<?php _htmlsc($invoice['supplier_invoice_date'] ?? ''); ?>" required></div><label class="col-sm-1 control-label"><?php _trans('due'); ?></label><div class="col-sm-3"><input type="date" class="form-control" name="supplier_due_date" value="<?php _htmlsc($invoice['supplier_due_date'] ?? ''); ?>"></div></div>
        <div class="form-group"><label class="col-sm-3 control-label"><?php _trans('currency'); ?></label><div class="col-sm-2"><input class="form-control" name="currency_code" maxlength="3" value="<?php _htmlsc($invoice['currency_code'] ?? 'EUR'); ?>"></div></div>
        <div class="form-group"><label class="col-sm-3 control-label"><?php _trans('subtotal'); ?></label><div class="col-sm-3"><input class="form-control" name="subtotal" value="<?php _htmlsc($invoice['subtotal'] ?? ''); ?>"></div></div>
        <div class="form-group"><label class="col-sm-3 control-label"><?php _trans('tax_total'); ?></label><div class="col-sm-3"><input class="form-control" name="tax_total" value="<?php _htmlsc($invoice['tax_total'] ?? ''); ?>"></div></div>
        <div class="form-group"><label class="col-sm-3 control-label"><?php _trans('total'); ?></label><div class="col-sm-3"><input class="form-control" name="total" value="<?php _htmlsc($invoice['total'] ?? ''); ?>"></div></div>
        <div class="form-group"><label class="col-sm-3 control-label"><?php _trans('notes'); ?></label><div class="col-sm-6"><textarea class="form-control" name="notes"><?php _htmlsc($invoice['notes'] ?? ''); ?></textarea></div></div>

        <h3><?php _trans('items'); ?></h3>
        <?php $formItems = $items ?: [[]]; for ($row = 0; $row < max(3, count($formItems)); $row++) : $item = $formItems[$row] ?? []; ?>
            <div class="form-inline supplier-invoice-line" style="margin-bottom: 8px;"><input class="form-control" name="item_name[]" placeholder="<?php _htmlsc(trans('description')); ?>" value="<?php _htmlsc($item['item_name'] ?? ''); ?>"><input class="form-control" name="quantity[]" type="number" step="0.000001" placeholder="<?php _htmlsc(trans('quantity')); ?>" value="<?php _htmlsc($item['quantity'] ?? '1'); ?>"><input class="form-control" name="unit_price[]" type="number" step="0.01" placeholder="<?php _htmlsc(trans('unit_price')); ?>" value="<?php _htmlsc($item['unit_price'] ?? ''); ?>"><input class="form-control" name="tax_rate[]" type="number" step="0.01" placeholder="<?php _htmlsc(trans('tax_percent')); ?>" value="<?php _htmlsc($item['tax_rate'] ?? ''); ?>"><input class="form-control item-subtotal" name="item_subtotal[]" placeholder="<?php _htmlsc(trans('subtotal')); ?>" value="<?php _htmlsc($item['subtotal'] ?? ''); ?>" readonly><input class="form-control item-tax-total" name="tax_total[]" placeholder="<?php _htmlsc(trans('tax')); ?>" value="<?php _htmlsc($item['tax_total'] ?? ''); ?>" readonly><input class="form-control item-total" name="item_total[]" placeholder="<?php _htmlsc(trans('total')); ?>" value="<?php _htmlsc($item['total'] ?? ''); ?>" readonly></div>
        <?php endfor; ?>
        <button class="btn btn-primary" type="submit" name="btn_submit" value="1"><?php _trans('save'); ?></button>
        <button class="btn btn-default" type="submit" name="btn_cancel" value="1"><?php _trans('cancel'); ?></button>
    </form>
</div>
<script>
(function () {
    function recalculate() {
        var subtotal = 0;
        var taxTotal = 0;
        document.querySelectorAll('.supplier-invoice-line').forEach(function (line) {
            var quantity = parseFloat(line.querySelector('[name="quantity[]"]').value) || 0;
            var unitPrice = parseFloat(line.querySelector('[name="unit_price[]"]').value) || 0;
            var taxRate = parseFloat(line.querySelector('[name="tax_rate[]"]').value) || 0;
            var lineSubtotal = Math.round(quantity * unitPrice * 100) / 100;
            var lineTax = Math.round(lineSubtotal * taxRate) / 100;
            line.querySelector('.item-subtotal').value = lineSubtotal.toFixed(2);
            line.querySelector('.item-tax-total').value = lineTax.toFixed(2);
            line.querySelector('.item-total').value = (lineSubtotal + lineTax).toFixed(2);
            subtotal += lineSubtotal;
            taxTotal += lineTax;
        });
        document.querySelector('[name="subtotal"]').value = subtotal.toFixed(2);
        document.querySelector('[name="tax_total"]').value = taxTotal.toFixed(2);
        document.querySelector('[name="total"]').value = (subtotal + taxTotal).toFixed(2);
    }
    document.querySelectorAll('.supplier-invoice-line input').forEach(function (input) {
        input.addEventListener('input', recalculate);
    });
    recalculate();
}());
</script>
