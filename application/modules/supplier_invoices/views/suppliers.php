<div id="headerbar">
    <h1 class="headerbar-title">Suppliers</h1>
    <div class="headerbar-item pull-right"><a class="btn btn-sm btn-primary" href="<?php echo site_url('supplier_invoices/supplier_form'); ?>">New supplier</a> <a class="btn btn-sm btn-default" href="<?php echo site_url('supplier_invoices'); ?>">Supplier invoices</a></div>
</div>
<div id="content" class="table-content">
    <?php $this->layout->load_view('layout/alerts'); ?>
    <table class="table table-striped"><thead><tr><th>Name</th><th>VAT ID</th><th>Peppol ID</th><th>Email</th><th>Status</th><th></th></tr></thead><tbody>
    <?php if (empty($suppliers)) : ?><tr><td colspan="6" class="text-center text-muted">No suppliers found.</td></tr><?php endif; ?>
    <?php foreach ($suppliers as $supplier) : ?><tr><td><?php _htmlsc($supplier['supplier_name']); ?></td><td><?php _htmlsc($supplier['supplier_vat_id'] ?? '—'); ?></td><td><?php _htmlsc($supplier['supplier_peppol_id'] ?? '—'); ?></td><td><?php _htmlsc($supplier['supplier_email'] ?? '—'); ?></td><td><?php echo (int) $supplier['supplier_active'] === 1 ? 'Active' : 'Inactive'; ?></td><td><a class="btn btn-xs btn-default" href="<?php echo site_url('supplier_invoices/supplier_form/' . (int) $supplier['supplier_id']); ?>">Edit</a></td></tr><?php endforeach; ?>
    </tbody></table>
</div>
