<div id="headerbar">
    <h1 class="headerbar-title">Supplier invoice <?php _htmlsc($invoice['supplier_invoice_number'] ?? ''); ?></h1>
    <div class="headerbar-item pull-right"><a class="btn btn-sm btn-default" href="<?php echo site_url('supplier_invoices'); ?>">Back</a></div>
</div>

<div id="content">
    <?php $this->layout->load_view('layout/alerts'); ?>
    <dl class="dl-horizontal">
        <dt>Supplier</dt><dd><?php _htmlsc($invoice['supplier_name'] ?? 'Unknown supplier'); ?></dd>
        <dt>Invoice date</dt><dd><?php _htmlsc($invoice['supplier_invoice_date'] ?? '—'); ?></dd>
        <dt>Due date</dt><dd><?php _htmlsc($invoice['supplier_due_date'] ?? '—'); ?></dd>
        <dt>Total</dt><dd><?php _htmlsc($invoice['total'] ?? '—'); ?> <?php _htmlsc($invoice['currency_code'] ?? 'EUR'); ?></dd>
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

    <h3>Status history</h3>
    <table class="table table-striped"><thead><tr><th>Date</th><th>From</th><th>To</th><th>Comment</th></tr></thead><tbody>
    <?php foreach ($history as $entry) : ?><tr><td><?php _htmlsc($entry['created_at']); ?></td><td><?php _htmlsc($entry['old_status'] ?? '—'); ?></td><td><?php _htmlsc($entry['new_status']); ?></td><td><?php _htmlsc($entry['comment'] ?? ''); ?></td></tr><?php endforeach; ?>
    </tbody></table>
</div>
