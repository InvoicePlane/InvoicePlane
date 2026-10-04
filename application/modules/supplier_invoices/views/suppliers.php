<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('suppliers'); ?></h1>
    <div class="headerbar-item pull-right"><a class="btn btn-sm btn-primary" href="<?php echo site_url('supplier_invoices/supplier_form'); ?>"><?php _trans('new_supplier'); ?></a> <a class="btn btn-sm btn-default" href="<?php echo site_url('supplier_invoices'); ?>"><?php _trans('supplier_invoices'); ?></a></div>
</div>
<div id="content" class="table-content">
    <?php $this->layout->load_view('layout/alerts'); ?>
    <table class="table table-striped"><thead><tr><th><?php _trans('name'); ?></th><th><?php _trans('vat_id'); ?></th><th><?php _trans('peppol_id'); ?></th><th><?php _trans('email'); ?></th><th><?php _trans('status'); ?></th><th></th></tr></thead><tbody>
    <?php if (empty($suppliers)) : ?><tr><td colspan="6" class="text-center text-muted"><?php _trans('no_suppliers_found'); ?></td></tr><?php endif; ?>
    <?php foreach ($suppliers as $supplier) : ?><tr><td><?php _htmlsc($supplier['supplier_name']); ?></td><td><?php _htmlsc($supplier['supplier_vat_id'] ?? '—'); ?></td><td><?php _htmlsc($supplier['supplier_peppol_id'] ?? '—'); ?></td><td><?php _htmlsc($supplier['supplier_email'] ?? '—'); ?></td><td><?php echo (int) $supplier['supplier_active'] === 1 ? trans('active') : trans('inactive'); ?></td><td><a class="btn btn-xs btn-default" href="<?php echo site_url('supplier_invoices/supplier_form/' . (int) $supplier['supplier_id']); ?>"><?php _trans('edit'); ?></a></td></tr><?php endforeach; ?>
    </tbody></table>
</div>
