<script>
    $(function () {
        $('#save_client_note').click(function () {
            $.post('<?php echo site_url('clients/ajax/save_client_note'); ?>',
                {
                    client_id: $('#client_id').val(),
                    client_note: $('#client_note').val()
                }, function (data) {
                    var response = JSON.parse(data);
                    if (response.success == '1') {
                        // The validation was successful
                        $('.control-group').removeClass('error');
                        $('#client_note').val('');

                        $('#notes_list').load("<?php echo site_url('clients/ajax/load_client_notes'); ?>",
                            {
                                client_id: <?php echo $client->client_id; ?>
                            });
                    }
                    else {
                        // The validation was not successful
                        $('.control-group').removeClass('error');
                        for (var key in response.validation_errors) {
                            $('#' + key).parent().parent().addClass('error');
                        }
                    }
                });
        });

    });
</script>

<div id="headerbar">
    <div class="pull-left">
        <h1><?php echo $client->client_name; ?></h1>
    </div>
    <div class="pull-right btn-group">
        <a href="#" class="btn btn-sm btn-default client-create-quote"
           data-client-name="<?php echo $client->client_name; ?>">
            <i class="fa fa-file"></i> <?php echo lang('create_quote'); ?>
        </a>
        <a href="#" class="btn btn-sm btn-default client-create-invoice"
           data-client-name="<?php echo $client->client_name; ?>"><i
                class="fa fa-file-text"></i> <?php echo lang('create_invoice'); ?></a>
        <a href="<?php echo site_url('clients/form/' . $client->client_id); ?>"
           class="btn btn-sm btn-default">
            <i class="fa fa-edit"></i> <?php echo lang('edit'); ?>
        </a>

        <a class="btn btn-sm btn-danger"
           href="<?php echo site_url('clients/delete/' . $client->client_id); ?>"
           onclick="return confirm('<?php echo lang('delete_client_warning'); ?>');">
            <i class="fa fa-trash-o"></i> <?php echo lang('delete'); ?>
        </a>
    </div>

</div>

<ul id="settings-tabs" class="nav nav-tabs nav-tabs-noborder">
    <li class="active"><a data-toggle="tab" href="#clientDetails"><?php echo lang('details'); ?></a></li>
    <li><a data-toggle="tab" href="#clientQuotes"><?php echo lang('quotes'); ?></a></li>
    <li><a data-toggle="tab" href="#clientInvoices"><?php echo lang('invoices'); ?></a></li>
    <li><a data-toggle="tab" href="#clientPayments"><?php echo lang('payments'); ?></a></li>
</ul>

<div class="tabbable tabs-below">

    <div class="tab-content">

        <div id="clientDetails" class="tab-pane tab-info active">

            <?php $this->layout->load_view('layout/alerts'); ?>

            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-8">
                    <h3><?php echo $client->client_name; ?></h3>

                    <p>
                        <?php echo ($client->client_address_1) ? $client->client_address_1 . '<br>' : ''; ?>
                        <?php echo ($client->client_address_2) ? $client->client_address_2 . '<br>' : ''; ?>
                        <?php echo ($client->client_city) ? $client->client_city : ''; ?>
                        <?php echo ($client->client_state) ? $client->client_state : ''; ?>
                        <?php echo ($client->client_zip) ? $client->client_zip : ''; ?>
                        <?php echo ($client->client_country) ? '<br>' . $client->client_country : ''; ?>
                    </p>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                    <table class="table table-condensed table-bordered">
                        <tr>
                            <th>
                                <?php echo lang('total_billed'); ?>
                            </th>
                            <td class="td-amount">
                                <?php echo format_currency($client->client_invoice_total); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php echo lang('total_paid'); ?>
                            </th>
                            <th class="td-amount">
                                <?php echo format_currency($client->client_invoice_paid); ?>
                            </th>
                        </tr>
                        <tr>
                            <th>
                                <?php echo lang('total_balance'); ?>
                            </th>
                            <td class="td-amount">
                                <?php echo format_currency($client->client_invoice_balance); ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <h4><?php echo lang('contact_information'); ?></h4>
                    <br>
                    <table class="table table-condensed table-striped">
                        <?php if ($client->client_email) : ?>
                            <tr>
                                <th><?php echo lang('email'); ?></th>
                                <td><?php echo auto_link($client->client_email, 'email'); ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($client->client_phone) : ?>
                            <tr>
                                <th><?php echo lang('phone'); ?></th>
                                <td><?php echo $client->client_phone; ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($client->client_mobile) : ?>
                            <tr>
                                <th><?php echo lang('mobile'); ?></th>
                                <td><?php echo $client->client_mobile; ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($client->client_fax) : ?>
                            <tr>
                                <th><?php echo lang('fax'); ?></th>
                                <td><?php echo $client->client_fax; ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($client->client_web) : ?>
                            <tr>
                                <th><?php echo lang('web'); ?></th>
                                <td><?php echo auto_link($client->client_web, 'url', true); ?></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="col-xs-12 col-md-6">
                    <h4><?php echo lang('tax_information'); ?></h4>
                    <br/>
                    <table class="table table-condensed table-striped">
                        <?php if ($client->client_vat_id) : ?>
                            <tr>
                                <th><?php echo lang('vat_id'); ?></th>
                                <td><?php echo $client->client_vat_id; ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($client->client_tax_code) : ?>
                            <tr>
                                <th><?php echo lang('tax_code'); ?></th>
                                <td><?php echo $client->client_tax_code; ?></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <?php if ($custom_fields) : ?>
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <h4><?php echo lang('custom_fields'); ?></h4>
                        <br>
                        <table class="table table-condensed table-striped">
                            <?php foreach ($custom_fields as $custom_field) : ?>
                                <tr>
                                    <th><?php echo $custom_field->custom_field_label ?></th>
                                    <td><?php echo nl2br($client->{$custom_field->custom_field_column}); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <hr>

            <div>
                <h4><?php echo lang('notes'); ?></h4>
                <br>

                <div id="notes_list">
                    <?php echo $partial_notes; ?>
                </div>
                <div class="panel panel-default panel-body">
                    <form class="row">
                        <div class="col-xs-12 col-md-10">
                            <input type="hidden" name="client_id" id="client_id"
                                   value="<?php echo $client->client_id; ?>">
                            <textarea id="client_note" class="form-control" rows="1"></textarea>
                        </div>
                        <div class="col-xs-12 col-md-2 text-center">
                            <input type="button" id="save_client_note" class="btn btn-default btn-block"
                                   value="<?php echo lang('add_notes'); ?>">
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <div id="clientQuotes" class="tab-pane table-content">
            <?php echo $quote_table; ?>
        </div>

        <div id="clientInvoices" class="tab-pane table-content">
            <?php echo $invoice_table; ?>
        </div>

        <div id="clientPayments" class="tab-pane table-content">
            <?php echo $payment_table; ?>
        </div>
    </div>

</div>