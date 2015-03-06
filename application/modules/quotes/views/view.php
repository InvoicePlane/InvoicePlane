<script type="text/javascript">

    $(function () {

        $('#btn_add_product').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('products/ajax/modal_product_lookups'); ?>/" + Math.floor(Math.random() * 1000));
        });

        $('#btn_add_row').click(function () {
            $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
        });

        <?php if (!$items) { ?>
        $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
        <?php } ?>

        $('#btn_save_quote').click(function () {
            var items = [];
            var item_order = 1;
            $('table tr.item').each(function () {
                var row = {};
                $(this).find('input,select,textarea').each(function () {
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
            $.post("<?php echo site_url('quotes/ajax/save'); ?>", {
                    quote_id: <?php echo $quote_id; ?>,
                    quote_number: $('#quote_number').val(),
                    quote_date_created: $('#quote_date_created').val(),
                    quote_date_expires: $('#quote_date_expires').val(),
                    quote_status_id: $('#quote_status_id').val(),
                    items: JSON.stringify(items),
                    custom: $('input[name^=custom]').serializeArray()
                },
                function (data) {
                    var response = JSON.parse(data);
                    if (response.success == '1') {
                        window.location = "<?php echo site_url('quotes/view'); ?>/" + <?php echo $quote_id; ?>;
                    }
                    else {
                        $('.control-group').removeClass('error');
                        for (var key in response.validation_errors) {
                            $('#' + key).parent().parent().addClass('error');
                        }
                    }
                });
        });

        $('#btn_generate_pdf').click(function () {
            window.open('<?php echo site_url('quotes/generate_pdf/' . $quote_id); ?>', '_blank');
        });

        var fixHelper = function (e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function (index) {
                $(this).width($originals.eq(index).width())
            });
            return $helper;
        };

        $("#item_table tbody").sortable({
            helper: fixHelper
        });

    });

</script>

<?php echo $modal_delete_quote; ?>
<?php echo $modal_add_quote_tax; ?>

<div class="headerbar">
    <h1><?php echo lang('quote'); ?> #<?php echo $quote->quote_number; ?></h1>

    <div class="pull-right btn-group">

        <div class="options btn-group pull-left">
            <a class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                <?php echo lang('options'); ?> <i class="fa fa-chevron-down"></i>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="#add-quote-tax" data-toggle="modal">
                        <i class="fa fa-plus fa-margin"></i>
                        <?php echo lang('add_quote_tax'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_generate_pdf"
                       data-quote-id="<?php echo $quote_id; ?>">
                        <i class="fa fa-print fa-margin"></i>
                        <?php echo lang('download_pdf'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo site_url('mailer/quote/' . $quote->quote_id); ?>">
                        <i class="fa fa-send fa-margin"></i>
                        <?php echo lang('send_email'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_quote_to_invoice"
                       data-quote-id="<?php echo $quote_id; ?>">
                        <i class="fa fa-refresh fa-margin"></i>
                        <?php echo lang('quote_to_invoice'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_copy_quote"
                       data-quote-id="<?php echo $quote_id; ?>">
                        <i class="fa fa-copy fa-margin"></i>
                        <?php echo lang('copy_quote'); ?>
                    </a>
                </li>
                <li>
                    <a href="#delete-quote" data-toggle="modal">
                        <i class="fa fa-trash-o fa-margin"></i> <?php echo lang('delete'); ?>
                    </a>
                </li>
            </ul>
        </div>

        <a href="#" class="btn btn-sm btn-default" id="btn_add_row">
            <i class="fa fa-plus"></i>
            <?php echo lang('add_new_row'); ?>
        </a>
        <a href="#" class="btn btn-sm btn-default" id="btn_add_product">
            <i class="fa fa-database"></i>
            <?php echo lang('add_product'); ?>
        </a>

        <a href="#" class="btn btn-sm btn-success" id="btn_save_quote">
            <i class="fa fa-check"></i>
            <?php echo lang('save'); ?>
        </a>
    </div>

</div>

<div class="content">

    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <form id="quote_form">

        <div class="quote">

            <div class="cf row">

                <div class="col-xs-12 col-md-8">
                    <div class="pull-left">

                        <h2>
                            <a href="<?php echo site_url('clients/view/' . $quote->client_id); ?>"><?php echo $quote->client_name; ?></a>
                        </h2><br>
					<span>
						<?php echo ($quote->client_address_1) ? $quote->client_address_1 . '<br>' : ''; ?>
                        <?php echo ($quote->client_address_2) ? $quote->client_address_2 . '<br>' : ''; ?>
                        <?php echo ($quote->client_city) ? $quote->client_city : ''; ?>
                        <?php echo ($quote->client_state) ? $quote->client_state : ''; ?>
                        <?php echo ($quote->client_zip) ? $quote->client_zip : ''; ?>
                        <?php echo ($quote->client_country) ? '<br>' . $quote->client_country : ''; ?>
					</span>
                        <br><br>
                        <?php if ($quote->client_phone) { ?>
                            <span><strong><?php echo lang('phone'); ?>
                                    :</strong> <?php echo $quote->client_phone; ?></span><br>
                        <?php } ?>
                        <?php if ($quote->client_email) { ?>
                            <span><strong><?php echo lang('email'); ?>
                                    :</strong> <?php echo $quote->client_email; ?></span>
                        <?php } ?>

                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="details-box">

                        <div class="quote-properties">
                            <label for="quote_number">
                                <?php echo lang('quote'); ?> #
                            </label>

                            <div class="controls">
                                <input type="text" id="quote_number" class="form-control input-sm"
                                       value="<?php echo $quote->quote_number; ?>">
                            </div>
                        </div>
                        <div class="quote-properties has-feedback">
                            <label for="quote_date_created">
                                <?php echo lang('date'); ?>
                            </label>

                            <div class="input-group">
                                <input name="quote_date_created" id="quote_date_created"
                                       class="form-control input-sm datepicker"
                                       value="<?php echo date_from_mysql($quote->quote_date_created); ?>">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar fa-fw"></i>
                                </span>
                            </div>
                        </div>
                        <div class="quote-properties has-feedback">
                            <label for="quote_date_expires">
                                <?php echo lang('expires'); ?>
                            </label>

                            <div class="input-group">
                                <input name="quote_date_expires" id="quote_date_expires"
                                       class="form-control input-sm datepicker"
                                       value="<?php echo date_from_mysql($quote->quote_date_expires); ?>">
                              <span class="input-group-addon">
                                  <i class="fa fa-calendar fa-fw"></i>
                              </span>
                            </div>
                        </div>
                        <div class="quote-properties">
                            <label for="quote_status_id">
                                <?php echo lang('status'); ?>
                            </label>
                            <select name="quote_status_id" id="quote_status_id"
                                    class="form-control input-sm">
                                <?php foreach ($quote_statuses as $key => $status) { ?>
                                    <option value="<?php echo $key; ?>"
                                            <?php if ($key == $quote->quote_status_id) { ?>selected="selected"<?php } ?>>
                                        <?php echo $status['label']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php $this->layout->load_view('quotes/partial_item_table'); ?>

        <?php foreach ($custom_fields as $custom_field) { ?>
        <p>
            <strong>
                <?php echo $custom_field->custom_field_label; ?>
            </strong>

            <input type="text" class="form-control"
                   name="custom[<?php echo $custom_field->custom_field_column; ?>]"
                   id="<?php echo $custom_field->custom_field_column; ?>"
                   value="<?php echo form_prep($this->mdl_quotes->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>"
                >
            <?php } ?>

        </p>

        <?php if ($quote->quote_status_id != 1) { ?>
            <p class="padded">
                <?php echo lang('guest_url'); ?>
                : <?php echo auto_link(site_url('guest/view/quote/' . $quote->quote_url_key)); ?>
            </p>
        <?php } ?>
</div>

</form>

</div>
