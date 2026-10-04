    <div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('supplier_invoice'); ?> <?php _htmlsc($invoice['supplier_invoice_number'] ?? ''); ?></h1>
    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('supplier_invoices/form/' . (int) $invoice['supplier_invoice_id']); ?>"><?php _trans('edit'); ?></a>
        <a class="btn btn-sm btn-default" href="<?php echo site_url('supplier_invoices'); ?>"><?php _trans('back'); ?></a>
    </div>
</div>

<div id="content">
    <?php $this->layout->load_view('layout/alerts'); ?>
    <dl class="dl-horizontal">
        <dt><?php _trans('supplier'); ?></dt><dd><?php _htmlsc($invoice['supplier_name'] ?? trans('unknown_supplier')); ?></dd>
        <dt><?php _trans('invoice_date'); ?></dt><dd><?php _htmlsc($invoice['supplier_invoice_date'] ?? '—'); ?></dd>
        <dt><?php _trans('due_date'); ?></dt><dd><?php _htmlsc($invoice['supplier_due_date'] ?? '—'); ?></dd>
        <dt><?php _trans('total'); ?></dt><dd><?php _htmlsc($invoice['total'] ?? '—'); ?> <?php _htmlsc($invoice['currency_code'] ?? 'EUR'); ?></dd>
        <dt><?php _trans('amount_paid'); ?></dt><dd><?php _htmlsc($invoice['amount_paid'] ?? '0'); ?> <?php _htmlsc($invoice['currency_code'] ?? 'EUR'); ?></dd>
        <dt><?php _trans('balance'); ?></dt><dd><?php _htmlsc($invoice['balance'] ?? '—'); ?> <?php _htmlsc($invoice['currency_code'] ?? 'EUR'); ?></dd>
        <dt><?php _trans('status'); ?></dt><dd><?php _htmlsc($invoice['status']); ?></dd>
        <dt><?php _trans('archive_status'); ?></dt><dd><?php echo $invoice['archived_at'] === null ? trans('active') : trans('archived'); ?><?php if ($invoice['archived_at'] !== null) : ?> (<?php _htmlsc($invoice['archived_at']); ?>)<?php endif; ?></dd>
    </dl>

    <?php if ($invoice['archived_at'] === null) : ?>
        <form method="post" action="<?php echo site_url('supplier_invoices/archive/' . (int) $invoice['supplier_invoice_id']); ?>" style="margin-bottom: 15px;">
            <?php _csrf_field(); ?><button class="btn btn-warning" type="submit"><?php _trans('archive_invoice'); ?></button>
        </form>
    <?php else : ?>
        <form method="post" action="<?php echo site_url('supplier_invoices/restore/' . (int) $invoice['supplier_invoice_id']); ?>" style="margin-bottom: 15px;">
            <?php _csrf_field(); ?><button class="btn btn-success" type="submit"><?php _trans('restore_invoice'); ?></button>
        </form>
    <?php endif; ?>

    <form method="post" action="<?php echo site_url('supplier_invoices/status/' . (int) $invoice['supplier_invoice_id']); ?>" class="form-inline">
        <?php _csrf_field(); ?>
        <label for="status"><?php _trans('change_status'); ?></label>
        <select id="status" name="status" class="form-control">
            <?php foreach ($statuses as $status) : ?>
                <option value="<?php echo $status; ?>" <?php echo $invoice['status'] === $status ? 'selected' : ''; ?>><?php _htmlsc($status); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="comment" class="form-control" placeholder="<?php _htmlsc(trans('comment')); ?>">
        <button class="btn btn-primary" type="submit"><?php _trans('save'); ?></button>
    </form>

    <h3><?php _trans('items'); ?></h3>
    <table class="table table-striped"><thead><tr><th><?php _trans('description'); ?></th><th><?php _trans('quantity'); ?></th><th><?php _trans('unit_price'); ?></th><th><?php _trans('total'); ?></th></tr></thead><tbody>
    <?php if (empty($items)) : ?><tr><td colspan="4" class="text-muted"><?php _trans('no_items_imported'); ?></td></tr><?php endif; ?>
    <?php foreach ($items as $item) : ?><tr><td><?php _htmlsc($item['item_name']); ?></td><td><?php _htmlsc($item['quantity']); ?></td><td><?php _htmlsc($item['unit_price'] ?? '—'); ?></td><td><?php _htmlsc($item['total'] ?? '—'); ?></td></tr><?php endforeach; ?>
    </tbody></table>

    <h3><?php _trans('documents'); ?></h3>
    <?php if ( ! empty($invoice['document_path'])) : ?><p><a class="btn btn-xs btn-default" href="<?php echo site_url('supplier_invoices/download_document/' . (int) $invoice['supplier_invoice_id']); ?>"><i class="fa fa-download"></i> <?php _htmlsc($invoice['document_name'] ?? trans('original_document')); ?></a></p><?php endif; ?>
    <form method="post" enctype="multipart/form-data" action="<?php echo site_url('supplier_invoices/upload_attachment/' . (int) $invoice['supplier_invoice_id']); ?>" class="form-inline">
        <?php _csrf_field(); ?>
        <input type="file" name="attachment" accept=".pdf,.xml,.jpg,.jpeg,.png,.gif,.webp" required>
        <button class="btn btn-primary" type="submit"><?php _trans('upload_attachment'); ?></button>
    </form>
    <table class="table table-striped"><thead><tr><th><?php _trans('file'); ?></th><th><?php _trans('type'); ?></th><th><?php _trans('size'); ?></th><th><?php _trans('date'); ?></th><th></th></tr></thead><tbody>
    <?php if (empty($attachments)) : ?><tr><td colspan="5" class="text-muted"><?php _trans('no_additional_attachments'); ?></td></tr><?php endif; ?>
    <?php foreach ($attachments as $attachment) : ?><tr><td><?php _htmlsc($attachment['file_name']); ?></td><td><?php _htmlsc($attachment['mime_type']); ?></td><td><?php _htmlsc($attachment['file_size']); ?> <?php _trans('bytes'); ?></td><td><?php _htmlsc($attachment['created_at']); ?></td><td><a class="btn btn-xs btn-default" href="<?php echo site_url('supplier_invoices/download_attachment/' . (int) $attachment['supplier_invoice_attachment_id']); ?>"><?php _trans('download'); ?></a></td></tr><?php endforeach; ?>
    </tbody></table>

    <h3><?php _trans('payments'); ?></h3>
    <form method="post" action="<?php echo site_url('supplier_invoices/payment/' . (int) $invoice['supplier_invoice_id']); ?>" class="form-inline">
        <?php _csrf_field(); ?>
        <input type="date" name="payment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
        <input type="text" name="amount" class="form-control" placeholder="<?php _htmlsc(trans('amount')); ?>" required>
        <input type="text" name="payment_method" class="form-control" placeholder="<?php _htmlsc(trans('payment_method')); ?>">
        <input type="text" name="reference" class="form-control" placeholder="<?php _htmlsc(trans('reference')); ?>">
        <input type="text" name="notes" class="form-control" placeholder="<?php _htmlsc(trans('notes')); ?>">
        <input type="hidden" name="currency_code" value="<?php _htmlsc($invoice['currency_code'] ?? 'EUR'); ?>">
        <button class="btn btn-primary" type="submit"><?php _trans('record_payment'); ?></button>
    </form>
    <table class="table table-striped"><thead><tr><th><?php _trans('date'); ?></th><th><?php _trans('amount'); ?></th><th><?php _trans('method'); ?></th><th><?php _trans('reference'); ?></th><th><?php _trans('notes'); ?></th></tr></thead><tbody>
    <?php if (empty($payments)) : ?><tr><td colspan="5" class="text-muted"><?php _trans('no_payments_recorded'); ?></td></tr><?php endif; ?>
    <?php foreach ($payments as $payment) : ?><tr><td><?php _htmlsc($payment['payment_date']); ?></td><td><?php _htmlsc($payment['amount']); ?> <?php _htmlsc($payment['currency_code']); ?></td><td><?php _htmlsc($payment['payment_method'] ?? '—'); ?></td><td><?php _htmlsc($payment['reference'] ?? '—'); ?></td><td><?php _htmlsc($payment['notes'] ?? ''); ?></td></tr><?php endforeach; ?>
    </tbody></table>

    <h3><?php _trans('status_history'); ?></h3>
    <table class="table table-striped"><thead><tr><th><?php _trans('date'); ?></th><th><?php _trans('from'); ?></th><th><?php _trans('to'); ?></th><th><?php _trans('comment'); ?></th></tr></thead><tbody>
    <?php foreach ($history as $entry) : ?><tr><td><?php _htmlsc($entry['created_at']); ?></td><td><?php _htmlsc($entry['old_status'] ?? '—'); ?></td><td><?php _htmlsc($entry['new_status']); ?></td><td><?php _htmlsc($entry['comment'] ?? ''); ?></td></tr><?php endforeach; ?>
    </tbody></table>
</div>
