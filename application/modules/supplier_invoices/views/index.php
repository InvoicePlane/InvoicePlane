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
    <form method="get" class="form-inline" style="margin-bottom: 15px;">
        <input class="form-control" name="q" value="<?php _htmlsc($filters['q']); ?>" placeholder="Supplier, invoice number or reference">
        <select class="form-control" name="status"><option value="">All statuses</option><?php foreach ($statuses as $status) : ?><option value="<?php echo $status; ?>" <?php echo $filters['status'] === $status ? 'selected' : ''; ?>><?php _htmlsc($status); ?></option><?php endforeach; ?></select>
        <select class="form-control" name="archived"><option value="active" <?php echo $filters['archived'] === 'active' ? 'selected' : ''; ?>>Active invoices</option><option value="archived" <?php echo $filters['archived'] === 'archived' ? 'selected' : ''; ?>>Archived invoices</option><option value="all" <?php echo $filters['archived'] === 'all' ? 'selected' : ''; ?>>All invoices</option></select>
        <input type="date" class="form-control" name="date_from" value="<?php _htmlsc($filters['date_from']); ?>">
        <input type="date" class="form-control" name="date_to" value="<?php _htmlsc($filters['date_to']); ?>">
        <button class="btn btn-primary" type="submit">Filter</button>
        <a class="btn btn-default" href="<?php echo site_url('supplier_invoices'); ?>">Reset</a>
    </form>
    <div class="text-muted" style="margin-bottom: 10px;">Results: <?php echo (int) count($supplier_invoices); ?></div>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead><tr><th>Supplier</th><th>Invoice number</th><th>Date</th><th>Due date</th><th>Total</th><th>Paid</th><th>Balance</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php if (empty($supplier_invoices)) : ?>
                <tr><td colspan="9" class="text-center text-muted">No supplier invoices found.</td></tr>
            <?php else : ?>
                <?php foreach ($supplier_invoices as $invoice) : ?>
                    <tr>
                        <td><?php _htmlsc($invoice['supplier_name'] ?? 'Unknown supplier'); ?></td>
                        <td><?php _htmlsc($invoice['supplier_invoice_number'] ?? '—'); ?></td>
                        <td><?php _htmlsc($invoice['supplier_invoice_date'] ?? '—'); ?></td>
                        <td><?php _htmlsc($invoice['supplier_due_date'] ?? '—'); ?></td>
                        <td><?php _htmlsc($invoice['total'] ?? '—'); ?> <?php _htmlsc($invoice['currency_code'] ?? 'EUR'); ?></td>
                        <td><?php _htmlsc($invoice['amount_paid'] ?? '0'); ?> <?php _htmlsc($invoice['currency_code'] ?? 'EUR'); ?></td>
                        <td><?php _htmlsc($invoice['balance'] ?? '—'); ?> <?php _htmlsc($invoice['currency_code'] ?? 'EUR'); ?></td>
                        <td><?php _htmlsc($invoice['status']); ?></td>
                        <td><a class="btn btn-xs btn-default" href="<?php echo site_url('supplier_invoices/view/' . (int) $invoice['supplier_invoice_id']); ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($pagination !== '') : ?><div class="text-center"><?php echo $pagination; ?></div><?php endif; ?>
</div>
