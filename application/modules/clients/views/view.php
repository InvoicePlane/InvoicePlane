<script type="text/javascript">
    $(function() {
        $('#save_client_note').click(function()
        {
            $.post("<?php echo site_url('clients/ajax/save_client_note'); ?>",
                {
                    client_id: $('#client_id').val(),
                    client_note: $('#client_note').val()
                }, function(data) {
                    var response = JSON.parse(data);
                    if (response.success == '1')
                    {
                        // The validation was successful
                        $('.control-group').removeClass('error');
                        $('#client_note').val('');

                        $('#notes_list').load("<?php echo site_url('clients/ajax/load_client_notes'); ?>",
                            {
                                client_id: <?php echo $client->client_id; ?>
                            });
                    }
                    else
                    {
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

<style>
    body {
        padding-bottom: 50px !important;
    }
</style>

<div class="headerbar">
    <h1><?php echo $client->client_name; ?></h1>

    <div class="pull-right btn-group">
        <a href="#" class="btn btn-sm btn-default client-create-quote"
           data-client-name="<?php echo $client->client_name; ?>">
            <i class="fa fa-file"></i> <?php echo lang('create_quote'); ?>
        </a>
        <a href="#" class="btn btn-sm btn-default client-create-invoice" data-client-name="<?php echo $client->client_name; ?>"><i class="fa fa-file-text""></i> <?php echo lang('create_invoice'); ?></a>
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

<div class="tabbable tabs-below">

    <div class="tab-content">

        <div id="clientDetails" class="tab-pane tab-info active">

            <?php $this->layout->load_view('layout/alerts'); ?>

            <div class="profile">

                <div class="primaryInfo row">

                    <div class="pull-left">
                        <h2><?php echo $client->client_name; ?></h2>
                        <br>
						<span>
							<?php echo ($client->client_address_1) ? $client->client_address_1 . '<br>' : ''; ?>
                            <?php echo ($client->client_address_2) ? $client->client_address_2 . '<br>' : ''; ?>
                            <?php echo ($client->client_city) ? $client->client_city : ''; ?>
                            <?php echo ($client->client_state) ? $client->client_state : ''; ?>
                            <?php echo ($client->client_zip) ? $client->client_zip : ''; ?>
                            <?php echo ($client->client_country) ? '<br>' . $client->client_country : ''; ?>
						</span>
                    </div>

                    <div class="pull-right" style="text-align: right;">
                        <span>
                            <strong>
                                <?php echo lang('total_billed'); ?>:
                            </strong>
                            <?php echo format_currency($client->client_invoice_total); ?>
                        </span>
                        <br>
                        <span>
                            <strong>
                                <?php echo lang('total_paid'); ?>:
                            </strong>
                            <?php echo format_currency($client->client_invoice_paid); ?>
                        </span>
                        <br>
                        <span>
                            <strong>
                                <?php echo lang('total_balance'); ?>:
                            </strong>
                            <?php echo format_currency($client->client_invoice_balance); ?>
                        </span>
                    </div>

                </div>

                <dl>
                    <dt><span><?php echo lang('contact_information'); ?></span></dt>
                    <?php if ($client->client_email) { ?>
                        <dd><span><?php echo lang('email'); ?>:</span> <?php echo auto_link($client->client_email, 'email'); ?></dd>
                    <?php } ?>
                    <?php if ($client->client_phone) { ?>
                        <dd><span><?php echo lang('phone'); ?>:</span> <?php echo $client->client_phone; ?></dd>
                    <?php } ?>
                    <?php if ($client->client_mobile) { ?>
                        <dd><span><?php echo lang('mobile'); ?>:</span> <?php echo $client->client_mobile; ?></dd>
                    <?php } ?>
                    <?php if ($client->client_fax) { ?>
                        <dd><span><?php echo lang('fax'); ?>:</span> <?php echo $client->client_fax; ?></dd>
                    <?php } ?>
                    <?php if ($client->client_web) { ?>
                        <dd><span><?php echo lang('web'); ?>:</span> <?php echo auto_link($client->client_web,'url', TRUE); ?></dd>
                    <?php } ?>
                </dl>

                <dl>
                    <dt><span><?php echo lang('tax_information'); ?></span></dt>
                    <?php if ($client->client_vat_id) { ?>
                        <dd><span><?php echo lang('vat_id'); ?>:</span> <?php echo $client->client_vat_id; ?></dd>
                    <?php } ?>
                    <?php if ($client->client_tax_code) { ?>
                        <dd><span><?php echo lang('tax_code'); ?>:</span> <?php echo $client->client_tax_code; ?></dd>
                    <?php } ?>
                </dl>

                <dl class="profile-custom">
                    <dt><span><?php echo lang('custom_fields'); ?></span></dt>
                    <?php foreach ($custom_fields as $custom_field) { ?>
                        <dd><span><?php echo $custom_field->custom_field_label; ?>: </span> <?php echo $client->{$custom_field->custom_field_column}; ?></dd>
                    <?php } ?>
                </dl>

                <br>

            </div>

            <div class="notes">

                <div id="notes_list">
                    <?php echo $partial_notes; ?>
                </div>

                <form>
                    <input type="hidden" name="client_id" id="client_id" value="<?php echo $client->client_id; ?>">
                    <fieldset>

                        <legend><?php echo lang('notes'); ?></legend>
                        <div class="form-group">
                            <div class="controls">
                                <textarea id="client_note" class="form-control"></textarea>
                            </div>
                        </div>

                        <input type="button" id="save_client_note" class="btn btn-default"
                               value="<?php echo lang('add_notes'); ?>">
                    </fieldset>
                </form>

            </div>
        </div>

        <div id="clientQuotes" class="tab-pane">
            <?php echo $quote_table; ?>
        </div>

        <div id="clientInvoices" class="tab-pane">
            <?php echo $invoice_table; ?>
        </div>

        <div id="clientPayments" class="tab-pane">
            <?php echo $payment_table; ?>
        </div>
    </div>

</div>

<nav class="navbar navbar-inverse navbar-fixed-bottom" role="navigation"
     style="max-height: 50px !important;">
    <div class="container-fluid">
        <ul class="nav navbar-nav">
            <li class="active"><a data-toggle="tab" href="#clientDetails"><?php echo lang('details'); ?></a></li>
            <li><a data-toggle="tab" href="#clientQuotes"><?php echo lang('quotes'); ?></a></li>
            <li><a data-toggle="tab" href="#clientInvoices"><?php echo lang('invoices'); ?></a></li>
            <li><a data-toggle="tab" href="#clientPayments"><?php echo lang('payments'); ?></a></li>
        </ul>
    </div>
</nav>