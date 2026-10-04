<div id="headerbar">
    <h1 class="headerbar-title">Supplier accounting</h1>

    <div class="headerbar-item pull-right">
        <a href="<?php echo site_url('supplier_invoices'); ?>" class="btn btn-sm btn-primary">
            <i class="fa fa-book"></i> Supplier invoice module
        </a>
        <a href="<?php echo site_url('integrations/incoming'); ?>" class="btn btn-sm btn-default">
            <i class="fa fa-arrow-left"></i> Incoming invoices
        </a>
    </div>
</div>

<div id="content" class="table-content">
    <?php $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Supplier</th>
                <th>Invoice number</th>
                <th>Invoice date</th>
                <th>Due date</th>
                <th>Total</th>
                <th>Status</th>
                <th>Document</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($supplier_invoices)) : ?>
                <tr>
                    <td colspan="7" class="text-center text-muted">No supplier invoices have been imported.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($supplier_invoices as $invoice) : ?>
                    <tr>
                        <td><?php _htmlsc($invoice['supplier_name'] ?? 'Unknown supplier'); ?></td>
                        <td><?php _htmlsc($invoice['supplier_invoice_number'] ?? $invoice['external_reference'] ?? '—'); ?></td>
                        <td><?php _htmlsc($invoice['supplier_invoice_date'] ?? '—'); ?></td>
                        <td><?php _htmlsc($invoice['supplier_due_date'] ?? '—'); ?></td>
                        <td><?php _htmlsc($invoice['total'] ?? '—'); ?> <?php _htmlsc($invoice['currency_code'] ?? 'EUR'); ?></td>
                        <td>
                            <form method="post" action="<?php echo site_url('integrations/incoming/update_supplier_invoice_status/' . (int) $invoice['supplier_invoice_id']); ?>">
                                <?php _csrf_field(); ?>
                                <select name="status" class="form-control input-sm" onchange="this.form.submit()">
                                    <?php foreach (['received', 'approved', 'paid', 'rejected'] as $status) : ?>
                                        <option value="<?php echo $status; ?>" <?php echo $invoice['status'] === $status ? 'selected' : ''; ?>><?php _htmlsc($status); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </td>
                        <td>
                            <a href="<?php echo site_url('integrations/incoming/download/' . (int) $invoice['incoming_response_id']); ?>"
                               class="btn btn-xs btn-default">
                                <i class="fa fa-download"></i> Download
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
