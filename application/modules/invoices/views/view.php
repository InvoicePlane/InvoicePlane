<script type="text/javascript">

    $(function() {

        $('#btn_add_item_from_lookup').click(function() {
            $('#modal-placeholder').load("<?php echo site_url('item_lookups/ajax/modal_item_lookups'); ?>/" + Math.floor(Math.random()*1000));
        });

        $('#btn_add_item').click(function() {
            $('#new_item').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
        });

        <?php if (!$items) { ?>
        $('#new_item').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
        <?php } ?>

        $('#btn_create_recurring').click(function()
        {
            $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_create_recurring'); ?>", {invoice_id: <?php echo $invoice_id; ?>});
        });

        $('#btn_save_invoice').click(function() {
            var items = [];
            var item_order = 1;
            $('table tr.item').each(function() {
                var row = {};
                $(this).find('input,select,textarea').each(function() {
                    if ($(this).is(':checkbox')) {
                        row[$(this).attr('name')] = $(this).is(':checked');
                    } else {
                        row[$(this).attr('name')] = $(this).val();
                    }
                });
                row['item_order'] = item_order;
                item_order++;
                items.push(row);
            });
            $.post("<?php echo site_url('invoices/ajax/save'); ?>", {
                    invoice_id: <?php echo $invoice_id; ?>,
                    invoice_number: $('#invoice_number').val(),
                    invoice_date_created: $('#invoice_date_created').val(),
                    invoice_date_due: $('#invoice_date_due').val(),
                    invoice_status_id: $('#invoice_status_id').val(),
                    items: JSON.stringify(items),
                    invoice_terms: $('#invoice_terms').val(),
                    custom: $('input[name^=custom]').serializeArray()
                },
                function(data) {
                    var response = JSON.parse(data);
                    if (response.success == '1') {
                        window.location = "<?php echo site_url('invoices/view'); ?>/" + <?php echo $invoice_id; ?>;
                    }
                    else {
                        $('.control-group').removeClass('error');
                        for (var key in response.validation_errors) {
                            $('#' + key).parent().parent().addClass('error');
                        }
                    }
                });
        });

        $('#btn_generate_pdf').click(function() {
            window.location = '<?php echo site_url('invoices/generate_pdf/' . $invoice_id); ?>';
        });

        var fixHelper = function(e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function(index) {
                $(this).width($originals.eq(index).width())
            });
            return $helper;
        };

        $("#item_table tbody").sortable({
            helper: fixHelper
        });

    });

</script>

<?php echo $modal_delete_invoice; ?>
<?php echo $modal_add_invoice_tax; ?>

<div class="headerbar">
    <h1><?php echo lang('invoice'); ?> #<?php echo $invoice->invoice_number; ?>
        <?php if ($invoice->invoice_is_recurring) { ?><span class="label label-info" style="margin-left: 10px;"><?php echo lang('recurring'); ?></span><?php } ?>
    </h1>

    <div class="pull-right btn-group">

        <div class="options btn-group pull-left">
            <a class="btn btn-sm btn-default dropdown-toggle"
               data-toggle="dropdown" href="#">
                <?php echo lang('options'); ?> <i class="fa fa-caret-down no-margin"></i>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="#add-invoice-tax" data-toggle="modal">
                        <i class="fa fa-plus fa-margin"></i> <?php echo lang('add_invoice_tax'); ?></a></li>
                <li>
                    <a href="#" class="invoice-add-payment"
                       data-invoice-id="<?php echo $invoice->invoice_id; ?>"
                       data-invoice-balance="<?php echo $invoice->invoice_balance; ?>">
                        <i class="fa fa-money fa-margin"></i>
                        <?php echo lang('enter_payment'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_generate_pdf"
                       data-invoice-id="<?php echo $invoice_id; ?>">
                        <i class="fa fa-print fa-margin"></i>
                        <?php echo lang('download_pdf'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo site_url('mailer/invoice/' . $invoice->invoice_id); ?>">
                        <i class="fa fa-send fa-margin"></i>
                        <?php echo lang('send_email'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_copy_invoice"
                       data-invoice-id="<?php echo $invoice_id; ?>">
                        <i class="fa fa-copy fa-margin"></i>
                        <?php echo lang('copy_invoice'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_create_recurring"
                       data-invoice-id="<?php echo $invoice_id; ?>">
                        <i class="fa fa-repeat fa-margin"></i>
                        <?php echo lang('create_recurring'); ?>
                    </a>
                </li>
                <li>
                    <a href="#delete-invoice" data-toggle="modal">
                        <i class="fa fa-trash-o fa-margin"></i>
                        <?php echo lang('delete'); ?>
                    </a>
                </li>
            </ul>
        </div>

        <a href="#" class="btn btn-sm btn-default" id="btn_add_item">
            <i class="fa fa-plus"></i> <?php echo lang('add_item'); ?>
        </a>
        <a href="#" class="btn btn-sm btn-default" id="btn_add_item_from_lookup">
            <i class="fa fa-database"></i>
            <?php echo lang('add_item_from_lookup'); ?>
        </a>

        <a href="#" class="btn btn-sm btn-success" id="btn_save_invoice">
            <i class="fa fa-check"></i> <?php echo lang('save'); ?>
        </a>
    </div>

</div>

<div class="content">

    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <form id="invoice_form" class="form-horizontal">

        <div class="invoice">

            <div class="cf">

                <div class="col-xs-12 col-md-8">
                    <div class="pull-left">

                        <h2><a href="<?php echo site_url('clients/view/' . $invoice->client_id); ?>"><?php echo $invoice->client_name; ?></a></h2><br>
                        <span>
                            <?php echo ($invoice->client_address_1) ? $invoice->client_address_1 . '<br>' : ''; ?>
                            <?php echo ($invoice->client_address_2) ? $invoice->client_address_2 . '<br>' : ''; ?>
                            <?php echo ($invoice->client_city) ? $invoice->client_city : ''; ?>
                            <?php echo ($invoice->client_state) ? $invoice->client_state : ''; ?>
                            <?php echo ($invoice->client_zip) ? $invoice->client_zip : ''; ?>
                            <?php echo ($invoice->client_country) ? '<br>' . $invoice->client_country : ''; ?>
                        </span>
                        <br><br>
                        <?php if ($invoice->client_phone) { ?>
                            <span><strong><?php echo lang('phone'); ?>:</strong> <?php echo $invoice->client_phone; ?></span><br>
                        <?php } ?>
                        <?php if ($invoice->client_email) { ?>
                            <span><strong><?php echo lang('email'); ?>:</strong> <?php echo $invoice->client_email; ?></span>
                        <?php } ?>

                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="details-box">

                        <div class="invoice-properties">
                            <label><?php echo lang('invoice'); ?> #</label>
                            <div >
                                <input type="text" id="invoice_number"
                                       class="input-sm form-control"
                                       value="<?php echo $invoice->invoice_number; ?>" >
                            </div>
                        </div>
                        <div class="invoice-properties has-feedback">
                            <label><?php echo lang('date'); ?></label>
                            <div class="input-group">
                                <input name="invoice_date_created" id="invoice_date_created"
                                       class="form-control datepicker"
                                       value="<?php echo date_from_mysql($invoice->invoice_date_created); ?>">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar fa-fw"></i>
                                </span>
                            </div>
                        </div>
                        <div class="invoice-properties has-feedback">
                            <label><?php echo lang('due_date'); ?></label>
                            <div class="input-group">
                                <input name="invoice_date_due" id="invoice_date_due"
                                       class="form-control datepicker"
                                       value="<?php echo date_from_mysql($invoice->invoice_date_due); ?>">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar fa-fw"></i>
                                </span>
                            </div>
                        </div>
                        <div class="invoice-properties">
                            <label><?php echo lang('status'); ?></label>
                            <div>
                                <select name="invoice_status_id" id="invoice_status_id"
                                        class="form-control input-sm">
                                    <?php foreach ($invoice_statuses as $key=>$status) { ?>
                                        <option value="<?php echo $key; ?>" <?php if ($key == $invoice->invoice_status_id) { ?>selected="selected"<?php } ?>><?php echo $status['label']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <?php $this->layout->load_view('invoices/partial_item_table'); ?>

            <label><?php echo lang('invoice_terms'); ?></label>
            <textarea id="invoice_terms" name="invoice_terms"
                      class="form-control" rows="3"><?php echo $invoice->invoice_terms; ?></textarea>

            <?php foreach ($custom_fields as $custom_field) { ?>
                <label><?php echo $custom_field->custom_field_label; ?></label>
                <input type="text" class="form-control"
                       name="custom[<?php echo $custom_field->custom_field_column; ?>]"
                       id="<?php echo $custom_field->custom_field_column; ?>"
                       value="<?php echo form_prep($this->mdl_invoices->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>">
            <?php } ?>

            <?php if ($invoice->invoice_status_id != 1) { ?>
            <p class="padded">
                <?php echo lang('guest_url'); ?>: <?php echo auto_link(site_url('guest/view/invoice/' . $invoice->invoice_url_key)); ?>
            </p>
            <?php } ?>
        </div>

    </form>

</div>
