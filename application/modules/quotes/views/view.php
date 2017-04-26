<?php
$cv = $this->controller->view_data["custom_values"];
?>
<script>

    $(function () {
        $('.btn_add_product').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('products/ajax/modal_product_lookups'); ?>/" + Math.floor(Math.random() * 1000));
        });

        $('.btn_add_row').click(function () {
            $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
        });

        $('#quote_change_client').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_change_client'); ?>", {
                quote_id: <?php echo $quote_id; ?>,
                client_id: "<?php echo $this->db->escape_str($quote->client_id); ?>"
            });
        });

        <?php if (!$items) { ?>
        $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
        <?php } ?>

        $('#btn_save_quote').click(function () {
            var items = [];
            var item_order = 1;
            $('table tbody.item').each(function () {
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
                    quote_password: $('#quote_password').val(),
                    items: JSON.stringify(items),
                    quote_discount_amount: $('#quote_discount_amount').val(),
                    quote_discount_percent: $('#quote_discount_percent').val(),
                    notes: $('#notes').val(),
                    custom: $('input[name^=custom],select[name^=custom]').serializeArray()
                },
                function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);
                    if (response.success === 1) {
                        window.location = "<?php echo site_url('quotes/view'); ?>/" + <?php echo $quote_id; ?>;
                    } else {
                        $('#fullpage-loader').hide();
                        $('.control-group').removeClass('has-error');
                        $('div.alert[class*="alert-"]').remove();
                        var resp_errors = response.validation_errors,
                            all_resp_errors = '';

                        if (typeof(resp_errors) == "string") {
                            all_resp_errors = resp_errors;
                        } else {
                            for (var key in resp_errors) {
                                $('#' + key).parent().addClass('has-error');
                                all_resp_errors += resp_errors[key];
                            }
                        }

                        $('#quote_form').prepend('<div class="alert alert-danger">' + all_resp_errors + '</div>');
                    }
                });
        });

        $('#btn_generate_pdf').click(function () {
            window.open('<?php echo site_url('quotes/generate_pdf/' . $quote_id); ?>', '_blank');
        });

        $(document).ready(function () {
            if ($('#quote_discount_percent').val().length > 0) {
                $('#quote_discount_amount').prop('disabled', true);
            }
            if ($('#quote_discount_amount').val().length > 0) {
                $('#quote_discount_percent').prop('disabled', true);
            }
        });
        $('#quote_discount_amount').keyup(function () {
            if (this.value.length > 0) {
                $('#quote_discount_percent').prop('disabled', true);
            } else {
                $('#quote_discount_percent').prop('disabled', false);
            }
        });
        $('#quote_discount_percent').keyup(function () {
            if (this.value.length > 0) {
                $('#quote_discount_amount').prop('disabled', true);
            } else {
                $('#quote_discount_amount').prop('disabled', false);
            }
        });

        var fixHelper = function (e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function (index) {
                $(this).width($originals.eq(index).width())
            });
            return $helper;
        };

        $("#item_table").sortable({
            helper: fixHelper,
            items: 'tbody'
        });
    });
</script>

<?php echo $modal_delete_quote; ?>
<?php echo $modal_add_quote_tax; ?>

<div id="headerbar">
    <h1 class="headerbar-title">
        <?php
        echo trans('quote') . ' ';
        echo($quote->quote_number ? '#' . $quote->quote_number : $quote->quote_id);
        ?>
    </h1>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm">
            <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                <?php _trans('options'); ?> <i class="fa fa-chevron-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a href="#add-quote-tax" data-toggle="modal">
                        <i class="fa fa-plus fa-margin"></i>
                        <?php _trans('add_quote_tax'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_generate_pdf"
                       data-quote-id="<?php echo $quote_id; ?>">
                        <i class="fa fa-print fa-margin"></i>
                        <?php _trans('download_pdf'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo site_url('mailer/quote/' . $quote->quote_id); ?>">
                        <i class="fa fa-send fa-margin"></i>
                        <?php _trans('send_email'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_quote_to_invoice"
                       data-quote-id="<?php echo $quote_id; ?>">
                        <i class="fa fa-refresh fa-margin"></i>
                        <?php _trans('quote_to_invoice'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_copy_quote"
                       data-quote-id="<?php echo $quote_id; ?>"
                       data-client-id="<?php echo $quote->client_id; ?>">
                        <i class="fa fa-copy fa-margin"></i>
                        <?php _trans('copy_quote'); ?>
                    </a>
                </li>
                <li>
                    <a href="#delete-quote" data-toggle="modal">
                        <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                    </a>
                </li>
            </ul>
        </div>

        <a href="#" class="btn btn-success btn-sm ajax-loader" id="btn_save_quote">
            <i class="fa fa-check"></i>
            <?php _trans('save'); ?>
        </a>
    </div>

</div>

<div id="content">
    <?php echo $this->layout->load_view('layout/alerts'); ?>
    <form id="quote_form">
        <div class="quote">

            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-5">

                    <h3>
                        <a href="<?php echo site_url('clients/view/' . $quote->client_id); ?>">
                            <?php _htmlsc(format_client($quote)) ?>
                        </a>
                        <?php if ($quote->quote_status_id == 1) { ?>
                            <span id="quote_change_client" class="fa fa-edit cursor-pointer small"
                                  data-toggle="tooltip" data-placement="bottom"
                                  title="<?php _trans('change_client'); ?>"></span>
                        <?php } ?>
                    </h3>
                    <br>
                    <div class="client-address">
                        <?php $this->layout->load_view('clients/partial_client_address', array('client' => $quote)); ?>
                    </div>
                    <?php if ($quote->client_phone || $quote->client_email) : ?>
                        <hr>
                    <?php endif; ?>
                    <?php if ($quote->client_phone): ?>
                        <div>
                            <?php _trans('phone'); ?>:&nbsp;
                            <?php _htmlsc($quote->client_phone); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($quote->client_email): ?>
                        <div>
                            <?php _trans('email'); ?>:&nbsp;
                            <?php echo $quote->client_email; ?>
                        </div>
                    <?php endif; ?>

                </div>

                <div class="col-xs-12 visible-xs"><br></div>

                <div class="col-xs-12 col-sm-6 col-md-7">
                    <div class="details-box">
                        <div class="row">

                            <div class="col-xs-12 col-md-6">

                                <div class="quote-properties">
                                    <label for="quote_number">
                                        <?php _trans('quote'); ?> #
                                    </label>
                                    <input type="text" id="quote_number" class="form-control input-sm"
                                        <?php if ($quote->quote_number) : ?> value="<?php echo $quote->quote_number; ?>"
                                        <?php else : ?> placeholder="<?php _trans('not_set'); ?>"
                                        <?php endif; ?>>
                                </div>
                                <div class="quote-properties has-feedback">
                                    <label for="quote_date_created">
                                        <?php _trans('date'); ?>
                                    </label>
                                    <div class="input-group">
                                        <input name="quote_date_created" id="quote_date_created"
                                               class="form-control input-sm datepicker"
                                               value="<?php echo date_from_mysql($quote->quote_date_created); ?>"/>
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar fa-fw"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="quote-properties has-feedback">
                                    <label for="quote_date_expires">
                                        <?php _trans('expires'); ?>
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

                                <!-- Custom fields -->
                                <?php foreach ($custom_fields as $custom_field): ?>
                                    <?php if ($custom_field->custom_field_location != 1) {
                                        continue;
                                    } ?>
                                    <?php print_field($this->mdl_quotes, $custom_field, $cv); ?>
                                <?php endforeach; ?>

                            </div>
                            <div class="col-xs-12 col-md-6">

                                <div class="quote-properties">
                                    <label for="quote_status_id">
                                        <?php _trans('status'); ?>
                                    </label>
                                    <select name="quote_status_id" id="quote_status_id"
                                            class="form-control input-sm simple-select">
                                        <?php foreach ($quote_statuses as $key => $status) { ?>
                                            <option value="<?php echo $key; ?>"
                                                    <?php if ($key == $quote->quote_status_id) { ?>selected="selected"
                                                <?php } ?>>
                                                <?php echo $status['label']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="quote-properties">
                                    <label for="quote_password">
                                        <?php _trans('quote_password'); ?>
                                    </label>
                                    <input type="text" id="quote_password" class="form-control input-sm"
                                           value="<?php echo $quote->quote_password; ?>">
                                </div>

                                <?php if ($quote->quote_status_id != 1) { ?>
                                    <div class="quote-properties">
                                        <label for="quote-guest-url"><?php _trans('guest_url'); ?></label>
                                        <div class="input-group">
                                            <input type="text" id="quote-guest-url" readonly class="form-control"
                                                   value="<?php echo site_url('guest/view/quote/' . $quote->quote_url_key); ?>">
                                            <span class="input-group-addon to-clipboard cursor-pointer"
                                                  data-clipboard-target="#quote-guest-url">
                                                <i class="fa fa-clipboard fa-fw"></i>
                                            </span>
                                        </div>
                                    </div>
                                <?php } ?>

                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>

        <?php $this->layout->load_view('quotes/partial_item_table'); ?>

        <hr/>

        <div class="row">
            <div class="col-xs-12 col-md-6">

                <div class="panel panel-default no-margin">
                    <div class="panel-heading">
                        <?php _trans('notes'); ?>
                    </div>
                    <div class="panel-body">
                        <textarea name="notes" id="notes" rows="3"
                                  class="input-sm form-control"><?php _htmlsc($quote->notes); ?></textarea>
                    </div>
                </div>

                <div class="col-xs-12 visible-xs visible-sm"><br></div>

            </div>
            <div class="col-xs-12 col-md-6">

                <?php $this->layout->load_view('upload/dropzone-quote-html'); ?>

                <?php if ($custom_fields): ?>
                    <?php $cv = $this->controller->view_data["custom_values"]; ?>
                    <div class="row">
                        <div class="col-xs-12">

                            <hr>

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <?php _trans('custom_fields'); ?>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <?php $i = 0; ?>
                                            <?php foreach ($custom_fields as $custom_field): ?>
                                                <?php if ($custom_field->custom_field_location != 0) {
                                                    continue;
                                                } ?>
                                                <?php $i++; ?>
                                                <?php if ($i % 2 != 0): ?>
                                                    <?php print_field($this->mdl_quotes, $custom_field, $cv); ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="col-xs-6">
                                            <?php $i = 0; ?>
                                            <?php foreach ($custom_fields as $custom_field): ?>
                                                <?php if ($custom_field->custom_field_location != 0) {
                                                    continue;
                                                } ?>
                                                <?php $i++; ?>
                                                <?php if ($i % 2 == 0): ?>
                                                    <?php print_field($this->mdl_quotes, $custom_field, $cv); ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
    </form>
</div>

<?php $this->layout->load_view('upload/dropzone-quote-scripts'); ?>
