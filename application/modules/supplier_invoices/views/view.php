    <div id="headerbar">
    <h1 class="headerbar-title">Supplier invoice <?php _htmlsc($invoice['supplier_invoice_number'] ?? ''); ?></h1>
    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('supplier_invoices/form/' . (int) $invoice['supplier_invoice_id']); ?>">Edit</a>
        <a class="btn btn-sm btn-default" href="<?php echo site_url('supplier_invoices'); ?>">Back</a>
    </div>
</div>

<div id="content">
    <?php $this->layout->load_view('layout/alerts'); ?>
    <dl class="dl-horizontal">
        <dt>Supplier</dt><dd><?php _htmlsc($invoice['supplier_name'] ?? 'Unknown supplier'); ?></dd>
        <dt>Invoice date</dt><dd><?php _htmlsc($invoice['supplier_invoice_date'] ?? '—'); ?></dd>
        <dt>Due date</dt><dd><?php _htmlsc($invoice['supplier_due_date'] ?? '—'); ?></dd>
        <dt>Total</dt><dd><?php _htmlsc($invoice['total'] ?? '—'); ?> <?php _htmlsc($invoice['currency_code'] ?? 'EUR'); ?></dd>
        <dt>Amount paid</dt><dd><?php _htmlsc($invoice['amount_paid'] ?? '0'); ?> <?php _htmlsc($invoice['currency_code'] ?? 'EUR'); ?></dd>
        <dt>Balance</dt><dd><?php _htmlsc($invoice['balance'] ?? '—'); ?> <?php _htmlsc($invoice['currency_code'] ?? 'EUR'); ?></dd>
        <dt>Status</dt><dd><?php _htmlsc($invoice['status']); ?></dd>
    </dl>

    <form method="post" action="<?php echo site_url('supplier_invoices/status/' . (int) $invoice['supplier_invoice_id']); ?>" class="form-inline">
        <?php _csrf_field(); ?>
        <label for="status">Change status</label>
        <select id="status" name="status" class="form-control">
            <?php foreach ($statuses as $status) : ?>
                <option value="<?php echo $status; ?>" <?php echo $invoice['status'] === $status ? 'selected' : ''; ?>><?php _htmlsc($status); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="comment" class="form-control" placeholder="Comment">
        <button class="btn btn-primary" type="submit">Save</button>
    </form>

    <h3>Items</h3>
    <table class="table table-striped"><thead><tr><th>Description</th><th>Quantity</th><th>Unit price</th><th>Total</th></tr></thead><tbody>
    <?php if (empty($items)) : ?><tr><td colspan="4" class="text-muted">No items imported.</td></tr><?php endif; ?>
    <?php foreach ($items as $item) : ?><tr><td><?php _htmlsc($item['item_name']); ?></td><td><?php _htmlsc($item['quantity']); ?></td><td><?php _htmlsc($item['unit_price'] ?? '—'); ?></td><td><?php _htmlsc($item['total'] ?? '—'); ?></td></tr><?php endforeach; ?>
    </tbody></table>

    <h3>Documents</h3>
    <?php if ( ! empty($invoice['document_path'])) : ?><p><a class="btn btn-xs btn-default" href="<?php echo site_url('supplier_invoices/download_document/' . (int) $invoice['supplier_invoice_id']); ?>"><i class="fa fa-download"></i> <?php _htmlsc($invoice['document_name'] ?? 'Original document'); ?></a></p><?php endif; ?>
    <form method="post" enctype="multipart/form-data" action="<?php echo site_url('supplier_invoices/upload_attachment/' . (int) $invoice['supplier_invoice_id']); ?>" class="form-inline">
        <?php _csrf_field(); ?>
        <input type="file" name="attachment" accept=".pdf,.xml,.jpg,.jpeg,.png,.gif,.webp" required>
        <button class="btn btn-primary" type="submit">Upload attachment</button>
    </form>
    <table class="table table-striped"><thead><tr><th>File</th><th>Type</th><th>Size</th><th>Date</th><th></th></tr></thead><tbody>
    <?php if (empty($attachments)) : ?><tr><td colspan="5" class="text-muted">No additional attachments.</td></tr><?php endif; ?>
    <?php foreach ($attachments as $attachment) : ?><tr><td><?php _htmlsc($attachment['file_name']); ?></td><td><?php _htmlsc($attachment['mime_type']); ?></td><td><?php _htmlsc($attachment['file_size']); ?> bytes</td><td><?php _htmlsc($attachment['created_at']); ?></td><td><a class="btn btn-xs btn-default" href="<?php echo site_url('supplier_invoices/download_attachment/' . (int) $attachment['supplier_invoice_attachment_id']); ?>">Download</a></td></tr><?php endforeach; ?>
    </tbody></table>

    <h3>Payments</h3>
    <form method="post" action="<?php echo site_url('supplier_invoices/payment/' . (int) $invoice['supplier_invoice_id']); ?>" class="form-inline">
        <?php _csrf_field(); ?>
        <input type="date" name="payment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
        <input type="text" name="amount" class="form-control" placeholder="Amount" required>
        <input type="text" name="payment_method" class="form-control" placeholder="Payment method">
        <input type="text" name="reference" class="form-control" placeholder="Reference">
        <input type="text" name="notes" class="form-control" placeholder="Notes">
        <input type="hidden" name="currency_code" value="<?php _htmlsc($invoice['currency_code'] ?? 'EUR'); ?>">
        <button class="btn btn-primary" type="submit">Record payment</button>
    </form>
    <table class="table table-striped"><thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Reference</th><th>Notes</th></tr></thead><tbody>
    <?php if (empty($payments)) : ?><tr><td colspan="5" class="text-muted">No payments recorded.</td></tr><?php endif; ?>
    <?php foreach ($payments as $payment) : ?><tr><td><?php _htmlsc($payment['payment_date']); ?></td><td><?php _htmlsc($payment['amount']); ?> <?php _htmlsc($payment['currency_code']); ?></td><td><?php _htmlsc($payment['payment_method'] ?? '—'); ?></td><td><?php _htmlsc($payment['reference'] ?? '—'); ?></td><td><?php _htmlsc($payment['notes'] ?? ''); ?></td></tr><?php endforeach; ?>
    </tbody></table>

    <h3>Status history</h3>
    <table class="table table-striped"><thead><tr><th>Date</th><th>From</th><th>To</th><th>Comment</th></tr></thead><tbody>
    <?php foreach ($history as $entry) : ?><tr><td><?php _htmlsc($entry['created_at']); ?></td><td><?php _htmlsc($entry['old_status'] ?? '—'); ?></td><td><?php _htmlsc($entry['new_status']); ?></td><td><?php _htmlsc($entry['comment'] ?? ''); ?></td></tr><?php endforeach; ?>
    </tbody></table>
</div>
