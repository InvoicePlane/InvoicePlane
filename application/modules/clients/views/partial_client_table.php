<div class="table-responsive">
    <table class="table table-hover table-striped" id="sort">
        <thead>
        <tr>
            <th><?php _trans('active'); ?></th>
            <th><?php _trans('client_name'); ?></th>
            <th><?php _trans('email_address'); ?></th>
            <th><?php _trans('phone_number'); ?></th>
            <th class="amount"><?php _trans('balance'); ?></th>
            <th><?php _trans('options'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($records as $client) : ?>
            <tr>
				<td data-table-header="<?php _trans('active'); ?>">
					<?php echo ($client->client_active) ? '<span class="label active">' . trans('yes') . '</span>' : '<span class="label inactive">' . trans('no') . '</span>'; ?>
				</td>
                <td data-table-header="<?php _trans('client_name'); ?>"><?php echo anchor('clients/view/' . $client->client_id, htmlsc(format_client($client))); ?></td>
                <td data-table-header="<?php _trans('email_address'); ?>"><?php _htmlsc($client->client_email); ?></td>
                <td data-table-header="<?php _trans('phone_number'); ?>"><?php _htmlsc($client->client_phone ? $client->client_phone : ($client->client_mobile ? $client->client_mobile : '')); ?></td>
                <td data-table-header="<?php _trans('balance'); ?>"class="amount"><?php echo format_currency($client->client_invoice_balance); ?></td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo site_url('clients/view/' . $client->client_id); ?>">
                                    <i class="fa fa-eye fa-margin"></i> <?php _trans('view'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('clients/form/' . $client->client_id); ?>">
                                    <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="client-create-quote"
                                   data-client-id="<?php echo $client->client_id; ?>">
                                    <i class="fa fa-file fa-margin"></i> <?php _trans('create_quote'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="client-create-invoice"
                                   data-client-id="<?php echo $client->client_id; ?>">
                                    <i class="fa fa-file-text fa-margin"></i> <?php _trans('create_invoice'); ?>
                                </a>
                            </li>
                            <li>
                                <form action="<?php echo site_url('clients/delete/' . $client->client_id); ?>"
                                      method="POST">
                                    <?php _csrf_field(); ?>
                                    <button type="submit" class="dropdown-button"
                                            onclick="return confirm('<?php _trans('delete_client_warning'); ?>');">
                                        <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.12/js/dataTables.bootstrap.min.js"></script>
<script src "https://cdn.datatables.net/plug-ins/1.10.15/sorting/stringMonthYear.js"></script>

<script>
    $(document).ready(function() {
        $("#sort").DataTable({
            "paging": false,
            "searching": false,
            "info": false,
        });
    });
</script>
