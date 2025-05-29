            <tr>
		<td>
		${client[0].client_active==1 ? 
		"<span class='label active'><?php _trans('yes') ?></span" : 
		"<span class='label inactive'><?php _trans('no') ?></span" 
		}
		</td>
                <td>
			<a href="<?php echo site_url('clients/view/'); ?>${client[0].client_id}">${client.htmlsc_name}</a>
		</td>

<?php if (ip_atac() || ip_xtra() || ip_hbk()): ?>
	<td>${client[0].client_extended_customer_no ? client[0].client_extended_customer_no : ""}</td>
<?php endif; ?>

<?php if (ip_atac()): ?>
	<td>${client[0].client_extended_contract ? client[0].client_extended_contract : "" }</td>
	<td>${client[0].client_extended_direct_debit ? client[0].client_extended_direct_debit : "" }</td>
	<td>${client[0].client_extended_flags ? client[0].client_extended_flags : "" }</td>
<?php endif; ?>

                <td>${client[0].client_email ? client[0].client_email : "" }</td>
                <td>${client[0].client_phone ? client[0].client_phone : client[0].client_mobile}</td>
                <td class="amount">${client[0].client_invoice_balance}</td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo site_url('clients/view/'); ?>${client[0].client_id}">
                                    <i class="fa fa-eye fa-margin"></i> <?php _trans('view'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('clients/form/');?>${client[0].client_id}">
                                    <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="client-create-quote"
                                   data-client-id="${client[0].client_id}">
                                    <i class="fa fa-file fa-margin"></i> <?php _trans('create_quote'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="client-create-invoice"
                                   data-client-id="${client[0].client_id}">
                                    <i class="fa fa-file-text fa-margin"></i> <?php _trans('create_invoice'); ?>
                                </a>
                            </li>
                            <li>
                                <form action="<?php echo site_url('clients/delete/');?>${client[0].client_id}"
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
