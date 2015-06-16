<div class="table-responsive">
    <table class="table table-striped">

        <thead>
        <tr>
            <th><?php echo lang('invoice'); ?></th>
            <th><?php echo lang('created'); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($invoices_archive as $invoice) {
            ?>
            <tr>
                <td>
                    <a href="<?php echo site_url('invoices/download/' . basename($invoice)); ?>"
                       title="<?php echo lang('invoice'); ?>" target="_blank">
                        <?php echo basename($invoice); ?>
                    </a>
                </td>

                <td>
                    <?php echo date("F d Y H:i:s.", filemtime($invoice)); ?>
                </td>

            </tr>
        <?php } ?>
        </tbody>

    </table>
</div>