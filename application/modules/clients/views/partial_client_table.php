<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th><?php echo lang('client_name'); ?></th>
            <th><?php echo lang('email_address'); ?></th>
            <th><?php echo lang('phone_number'); ?></th>
            <th style="text-align: right;"><?php echo lang('balance'); ?></th>
            <th><?php echo lang('active'); ?></th>
            <th><?php echo lang('options'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($records as $client) : ?>
            <tr>
                <td><?php echo anchor('clients/view/' . $client->client_id, $client->client_name); ?></td>
                <td><?php echo $client->client_email; ?></td>
                <td><?php echo(($client->client_phone ? $client->client_phone : ($client->client_mobile ? $client->client_mobile : ''))); ?></td>
                <td style="text-align: right;"><?php echo format_currency($client->client_invoice_balance); ?></td>
                <td><?php echo ($client->client_active) ? lang('yes') : lang('no'); ?></td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> <?php echo lang('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo site_url('clients/view/' . $client->client_id); ?>">
                                    <i class="fa fa-eye fa-margin"></i> <?php echo lang('view'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('clients/form/' . $client->client_id); ?>">
                                    <i class="fa fa-edit fa-margin"></i> <?php echo lang('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="client-create-quote"
                                   data-client-name="<?php echo $client->client_name; ?>">
                                    <i class="fa fa-file fa-margin"></i> <?php echo lang('create_quote'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="client-create-invoice"
                                   data-client-name="<?php echo $client->client_name; ?>">
                                    <i class="fa fa-file-text fa-margin"></i> <?php echo lang('create_invoice'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('clients/delete/' . $client->client_id); ?>"
                                   onclick="return confirm('<?php echo lang('delete_client_warning'); ?>');">
                                    <i class="fa fa-trash-o fa-margin"></i> <?php echo lang('delete'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>