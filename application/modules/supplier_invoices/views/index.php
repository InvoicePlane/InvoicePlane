<div id="headerbar">
    <h1 class="headerbar-title">Supplier invoices</h1>
    <div class="headerbar-item pull-right">
        <a href="<?php echo site_url('supplier_invoices/form'); ?>" class="btn btn-sm btn-primary">
            <i class="fa fa-plus"></i> New supplier invoice
        </a>
        <a href="<?php echo site_url('supplier_invoices/suppliers'); ?>" class="btn btn-sm btn-default">
            <i class="fa fa-users"></i> Suppliers
        </a>
        <a href="<?php echo site_url('integrations/incoming'); ?>" class="btn btn-sm btn-default">
            <i class="fa fa-download"></i> Incoming invoices
        </a>
    </div>
</div>

<div id="content" class="table-content">
    <?php $this->layout->load_view('layout/alerts'); ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead><tr><th>Supplier</th><th>Invoice number</th><th>Date</th><th>Due date</th><th>Total</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php if (empty($supplier_invoices)) : ?>
                <tr><td colspan="7" class="text-center text-muted">No supplier invoices found.</td></tr>
            <?php else : ?>
                <?php foreach ($supplier_invoices as $invoice) : ?>
                    <tr>
                        <td><?php _htmlsc($invoice['supplier_name'] ?? 'Unknown supplier'); ?></td>
                        <td><?php _htmlsc($invoice['supplier_invoice_number'] ?? '—'); ?></td>
                        <td><?php _htmlsc($invoice['supplier_invoice_date'] ?? '—'); ?></td>
                        <td><?php _htmlsc($invoice['supplier_due_date'] ?? '—'); ?></td>
                        <td><?php _htmlsc($invoice['total'] ?? '—'); ?> <?php _htmlsc($invoice['currency_code'] ?? 'EUR'); ?></td>
                        <td><?php _htmlsc($invoice['status']); ?></td>
                        <td><a class="btn btn-xs btn-default" href="<?php echo site_url('supplier_invoices/view/' . (int) $invoice['supplier_invoice_id']); ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
