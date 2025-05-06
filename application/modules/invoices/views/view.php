<?php
if ($this->config->item('disable_read_only') == true) {
    $invoice->is_read_only = 0;
}
// Little helper
$its_mine = $this->session->__get('user_id') == $invoice->user_id;
$my_class = $its_mine ? 'success' : 'warning'; // visual: work with text-* alert-*
?>

<script>
    $(function () {
        $('.item-task-id').each(function () {
            // Disable client chaning if at least one item already has a task id assigned
            if ($(this).val().length > 0) {
                $('#invoice_change_client').hide();
                return false;
            }
        });

        $('.btn_add_product').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('products/ajax/modal_product_lookups'); ?>/" + Math.floor(Math.random() * 1000));
        });

        $('.btn_add_task').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('tasks/ajax/modal_task_lookups/' . $invoice_id); ?>/" + Math.floor(Math.random() * 1000));
        });

        $('.btn_add_row').click(function () {
            $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
            // Legacy:no: check items tax usage is correct (ReLoad on change)
            check_items_tax_usages();
        });

<?php
if (!$items) {
?>
        $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
<?php
}
?>

        // Legacy:no: check items tax usage is correct (Load on change)
        $(document).on('loaded', check_items_tax_usages());

        $('#btn_create_recurring').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_create_recurring'); ?>", {
                invoice_id: <?php echo $invoice_id; ?>
            });
        });
<?php
if ($invoice->invoice_status_id == 1 && !$invoice->creditinvoice_parent_id) {
?>

        $('#invoice_change_client').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_change_client'); ?>", {
                invoice_id: <?php echo $invoice_id; ?>,
                client_id: "<?php echo $this->db->escape_str($invoice->client_id); ?>",
            });
        });

        $('#invoice_change_user').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_change_user'); ?>", {
                invoice_id: <?php echo $invoice_id; ?>,
                user_id: "<?php echo $this->db->escape_str($invoice->user_id); ?>",
            });
        });
<?php
} // End if
?>

        $('#btn_save_invoice').click(function () {
            var items = [];
            var item_order = 1;
            $('#item_table .item').each(function () {
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
            $.post("<?php echo site_url('invoices/ajax/save'); ?>", {
                    invoice_id: <?php echo $invoice_id; ?>,
                    invoice_number: $('#invoice_number').val(),
                    invoice_date_created: $('#invoice_date_created').val(),
                    invoice_date_due: $('#invoice_date_due').val(),
                    invoice_status_id: $('#invoice_status_id').val(),
                    invoice_password: $('#invoice_password').val(),
                    items: JSON.stringify(items),
                    invoice_discount_amount: $('#invoice_discount_amount').val(),
                    invoice_discount_percent: $('#invoice_discount_percent').val(),
                    invoice_terms: $('#invoice_terms').val(),
                    custom: $('input[name^=custom],select[name^=custom]').serializeArray(),
                    payment_method: $('#payment_method').val(),
                },
                function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);
                    if (response.success === 1) {
                        window.location = "<?php echo site_url('invoices/view'); ?>/" + <?php echo $invoice_id; ?>;
                    } else {
                        $('#fullpage-loader').hide();
                        $('.control-group').removeClass('has-error');
                        $('div.alert[class*="alert-"]').remove();
                        var resp_errors = response.validation_errors,
                            all_resp_errors = '';
                        for (var key in resp_errors) {
                            $('#' + key).parent().addClass('has-error');
                            all_resp_errors += resp_errors[key];
                        }
                        $('#invoice_form').prepend('<div class="alert alert-danger">' + all_resp_errors + '</div>');
                    }
                });
        });

        $('#btn_generate_pdf').click(function () {
            window.open('<?php echo site_url('invoices/generate_pdf/' . $invoice_id); ?>', '_blank');
        });

        $(document).on('click', '.btn_delete_item', function () {
            var btn = $(this);
            var item_id = btn.data('item-id');

            // Just remove the row if no item ID is set (new row)
            if (typeof item_id === 'undefined') {
                $(this).parents('.item').remove();
            }

            $.post("<?php echo site_url('invoices/ajax/delete_item/' . $invoice->invoice_id); ?>", {
                    'item_id': item_id,
                },
                function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);

                    if (response.success === 1) {
                        btn.parents('.item').remove();
                    } else {
                        btn.removeClass('btn-link').addClass('btn-danger').prop('disabled', true);
                    }
                });
        });

<?php
if ($invoice->is_read_only != 1) {
            if (get_setting('show_responsive_itemlist') == 1) { ?>
             function UpR(k) {
               var parent = k.parents('.item');
               var pos = parent.prev();
               parent.insertBefore(pos);
             }
             function DownR(k) {
               var parent = k.parents('.item');
               var pos = parent.next();
               parent.insertAfter(pos);
             }
             $(document).on('click', '.up', function () {
               UpR($(this));
             });
             $(document).on('click', '.down', function () {
               DownR($(this));
             });
<?php
    } else {
?>
            var fixHelper = function (e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function (index) {
                    $(this).width($originals.eq(index).width());
                });
                return $helper;
            };

            $('#item_table').sortable({
                items: 'tbody',
                helper: fixHelper,
            });
<?php
    }
?>

        if ($('#invoice_discount_percent').val().length > 0) {
            $('#invoice_discount_amount').prop('disabled', true);
        }

        if ($('#invoice_discount_amount').val().length > 0) {
            $('#invoice_discount_percent').prop('disabled', true);
        }

        $('#invoice_discount_amount').keyup(function () {
            if (this.value.length > 0) {
                $('#invoice_discount_percent').prop('disabled', true);
            } else {
                $('#invoice_discount_percent').prop('disabled', false);
            }
        });
        $('#invoice_discount_percent').keyup(function () {
            if (this.value.length > 0) {
                $('#invoice_discount_amount').prop('disabled', true);
            } else {
                $('#invoice_discount_amount').prop('disabled', false);
            }
        });
<?php
}
?>

<?php if ($invoice->invoice_is_recurring) { ?>
        $(document).on('click', '.js-item-recurrence-toggler', function () {
            var itemRecurrenceState = $(this).next('input').val();
            if (itemRecurrenceState === ('1')) {
                $(this).next('input').val('0');
                $(this).removeClass('fa-calendar-check-o text-success');
                $(this).addClass('fa-calendar-o text-muted');
            } else {
                $(this).next('input').val('1');
                $(this).removeClass('fa-calendar-o text-muted');
                $(this).addClass('fa-calendar-check-o text-success');
            }
        });
<?php } ?>

    });
</script>

<?php
echo $modal_delete_invoice;
echo $legacy_calculation ? $modal_add_invoice_tax : ''; // Legacy calculation have global taxes - since v1.6.3
?>
<div id="headerbar">
    <h1 class="headerbar-title">
        <span data-toggle="tooltip" data-placement="bottom" title="<?php _trans('invoicing') ;?>: <?php _htmlsc(PHP_EOL . format_user($invoice->user_id)); ?>">
            <?php echo trans('invoice') . ' ' . ($invoice->invoice_number ? '#' . $invoice->invoice_number : trans('id') . ': ' . $invoice->invoice_id); ?>
        </span>
<?php
// Nb Admins > 1 only
if ($change_user) {
?>
        <a data-toggle="tooltip" data-placement="bottom"
           title="<?php _trans('edit') ;?> <?php _trans('user') ;?> (<?php _trans('invoicing') ;?>): <?php _htmlsc(PHP_EOL . format_user($invoice->user_id)); ?>"
           href="<?php echo site_url('users/form/' . $invoice->user_id); ?>">
            <i class="fa fa-xs fa-user text-<?php echo $my_class; ?>"></i>
                <span class="hidden-xs"><?php _htmlsc($invoice->user_name); ?></span>
        </a>
<?php
    if ($invoice->invoice_status_id == 1 && ! $invoice->creditinvoice_parent_id) {
?>

        <span id="invoice_change_user" class="fa fa-fw fa-edit text-<?php echo $its_mine ? 'muted' : 'danger'; ?> cursor-pointer"
              data-toggle="tooltip" data-placement="bottom"
              title="<?php _trans('change_user'); ?>"></span>
<?php
    } // End if draft
} // End if change_user
?>
    </h1>

    <div class="headerbar-item pull-right<?php echo ($invoice->is_read_only != 1 || $invoice->invoice_status_id != 4) ? ' btn-group' : ''; ?>">

        <div class="options btn-group btn-group-sm">
            <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-caret-down no-margin"></i> <?php _trans('options'); ?>
            </a>
            <ul class="dropdown-menu">
<?php
if ($legacy_calculation && $invoice->is_read_only != 1) { // Legacy calculation have global taxes - since v1.6.3
?>
                <li>
                    <a href="#add-invoice-tax" data-toggle="modal">
                        <i class="fa fa-plus fa-margin"></i> <?php _trans('add_invoice_tax'); ?>
                    </a>
                </li>
<?php
} // End if
?>
                <li>
                    <a href="#" id="btn_create_credit" data-invoice-id="<?php echo $invoice_id; ?>">
                        <i class="fa fa-minus fa-margin"></i> <?php _trans('create_credit_invoice'); ?>
                    </a>
                </li>
<?php
if ($invoice->invoice_balance != 0) {
?>
                <li>
                    <a href="#" class="invoice-add-payment"
                       data-invoice-id="<?php echo $invoice_id; ?>"
                       data-invoice-balance="<?php echo $invoice->invoice_balance; ?>"
                       data-invoice-payment-method="<?php echo $invoice->payment_method; ?>"
                       data-payment-cf-exist="<?php echo $payment_cf_exist ?? ''; ?>">
                        <i class="fa fa-credit-card fa-margin"></i>
                        <?php _trans('enter_payment'); ?>
                    </a>
                </li>
<?php
}
?>
                <li>
                    <a href="#" id="btn_generate_pdf"
                       data-invoice-id="<?php echo $invoice_id; ?>">
                        <i class="fa fa-print fa-margin"></i>
                        <?php _trans('download_pdf'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo site_url('mailer/invoice/' . $invoice->invoice_id); ?>">
                        <i class="fa fa-send fa-margin"></i>
                        <?php _trans('send_email'); ?>
                    </a>
                </li>
                <li class="divider"></li>
                <li>
                    <a href="#" id="btn_create_recurring"
                       data-invoice-id="<?php echo $invoice_id; ?>">
                        <i class="fa fa-refresh fa-margin"></i>
                        <?php _trans('create_recurring'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_copy_invoice"
                       data-invoice-id="<?php echo $invoice_id; ?>"
                       data-client-id="<?php echo $invoice->client_id; ?>">
                        <i class="fa fa-copy fa-margin"></i>
                        <?php _trans('copy_invoice'); ?>
                    </a>
                </li>
<?php
if ($invoice->invoice_status_id == 1 || ($this->config->item('enable_invoice_deletion') === true && $invoice->is_read_only != 1)) {
?>
                <li>
                    <a href="#delete-invoice" data-toggle="modal">
                        <i class="fa fa-trash-o fa-margin"></i>
                        <?php _trans('delete'); ?>
                    </a>
                </li>
<?php
} // End if
?>
            </ul>
        </div>

<?php
if ($invoice->is_read_only != 1 || $invoice->invoice_status_id != 4) {
?>
        <a href="#" class="btn btn-sm btn-success ajax-loader" id="btn_save_invoice">
            <i class="fa fa-check"></i> <?php _trans('save'); ?>
        </a>
<?php
} //End if
?>
    </div>

    <div class="headerbar-item invoice-labels pull-right">
<?php
if ($invoice->invoice_is_recurring) {
?>
        <span class="label label-info">
            <i class="fa fa-refresh"></i> <?php _trans('recurring'); ?>
        </span>
<?php
}
if ($invoice->is_read_only == 1) {
?>
        <span class="label label-danger">
            <i class="fa fa-read-only"></i> <?php _trans('read_only'); ?>
        </span>
<?php
}
?>
    </div>

</div>

<div id="content">

    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <div id="invoice_form">
        <div class="invoice">

            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-5">

                    <h2>
                        <a href="<?php echo site_url('clients/view/' . $invoice->client_id); ?>"><?php _htmlsc(format_client($invoice)); ?></a>
<?php
if ($invoice->invoice_status_id == 1 && !$invoice->creditinvoice_parent_id) {
?>
                        <span id="invoice_change_client" class="fa fa-edit cursor-pointer small"
                              data-toggle="tooltip" data-placement="bottom"
                              title="<?php _trans('change_client'); ?>"></span>
<?php
} // End if
?>
                    </h2>
                    <br>
                    <div class="client-address">
                        <?php $this->layout->load_view('clients/partial_client_address', ['client' => $invoice]); ?>
                    </div>
<?php if ($invoice->client_phone || $invoice->client_email) : ?>
                    <hr>
<?php endif; ?>
<?php if ($invoice->client_phone) : ?>
                    <div><?php _trans('phone'); ?>:&nbsp;<?php _htmlsc($invoice->client_phone); ?></div>
<?php endif; ?>
<?php if ($invoice->client_email) : ?>
                    <div><?php _trans('email'); ?>:&nbsp;<?php _auto_link($invoice->client_email); ?></div>
<?php endif; ?>

                </div>

                <div class="col-xs-12 visible-xs"><br></div>

                <div class="col-xs-12 col-sm-5 col-sm-offset-1 col-md-6 col-md-offset-1">
                    <div class="details-box panel panel-default panel-body">
                        <div class="row">
<?php
if ($invoice->invoice_sign == -1) {
    $parent_invoice_number = $this->mdl_invoices->get_parent_invoice_number($invoice->creditinvoice_parent_id);
    $view_link = anchor('/invoices/view/' . $invoice->creditinvoice_parent_id, trans('credit_invoice_for_invoice') . ' ' . $parent_invoice_number);
?>
                            <div class="col-xs-12">
                                <div class="alert alert-warning small">
                                    <i class="fa fa-credit-invoice"></i>&nbsp;<?php echo $view_link; ?>
                                </div>
                            </div>
<?php
} // End if
?>

                            <div class="col-xs-12 col-md-6">

                                <div class="invoice-properties">
<?php if ($einvoice->name) : ?>
                                    <span class="pull-right" id="e_invoice_active"
                                          data-toggle="tooltip" data-placement="bottom"
                                          title="e-<?php echo trans('invoice') . ' ' . ($einvoice->user ? trans('version') . ' ' . $einvoice->name . ' 🗸' : '🚫 ' . trans('einvoicing_user_fields_error')); ?>"
                                    >
                                        <i class="fa fa-file-code-o"></i>
                                        <?php echo $einvoice->name; ?>
                                        <i class="fa fa-<?php echo $einvoice->user ? 'check-square-o text-success' : 'user-times text-warning'; ?>"></i>
                                    </span>
<?php endif; ?>
                                    <label for="invoice_number"><?php _trans('invoice'); ?> #</label>
                                    <input type="text" id="invoice_number" class="form-control"
<?php if ($invoice->invoice_number) : ?>
                                           value="<?php echo $invoice->invoice_number; ?>"
<?php else : ?>
                                           placeholder="<?php _trans('not_set'); ?>"
<?php endif; ?>
                                           <?php echo $invoice->is_read_only ? 'disabled="disabled"' : '';?>
                                    >

                                </div>

                                <div class="invoice-properties has-feedback">
                                    <label><?php _trans('date'); ?></label>

                                    <div class="input-group">
                                        <input name="invoice_date_created" id="invoice_date_created"
                                               class="form-control datepicker"
                                               value="<?php echo date_from_mysql($invoice->invoice_date_created); ?>"
                                               <?php echo $invoice->is_read_only ? 'disabled="disabled"' : '';?>>
                                        <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                                    </div>
                                </div>

                                <div class="invoice-properties has-feedback">
                                    <label><?php _trans('due_date'); ?></label>

                                    <div class="input-group">
                                        <input name="invoice_date_due" id="invoice_date_due"
                                               class="form-control datepicker"
                                               value="<?php echo date_from_mysql($invoice->invoice_date_due); ?>"
                                               <?php echo $invoice->is_read_only ? 'disabled="disabled"' : '';?>>
                                        <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12 col-md-6">

                                <div class="invoice-properties">
                                    <label>
                                        <?php _trans('status');
                                        if ($invoice->is_read_only != 1 || $invoice->invoice_status_id != 4) {
                                            echo ' <span class="small">(' . trans('can_be_changed') . ')</span>';
                                        } ?>
                                    </label>
                                    <select name="invoice_status_id" id="invoice_status_id"
                                            class="form-control simple-select" data-minimum-results-for-search="Infinity"
                                            <?php echo ($invoice->is_read_only == 1 && $invoice->invoice_status_id == 4) ? 'disabled="disabled"' : ''; ?>
                                    >
<?php
foreach ($invoice_statuses as $key => $status) {
    $is_selected = ($key == $invoice->invoice_status_id) ? ' selected="selected"' : '';
?>
                                        <option value="<?php echo $key; ?>"<?php echo $is_selected; ?>>
                                            <?php echo $status['label']; ?>
                                        </option>
<?php
}
?>
                                    </select>
                                </div>

                                <div class="invoice-properties">
                                    <label><?php _trans('payment_method'); ?></label>
                                    <select name="payment_method" id="payment_method"
                                            class="form-control simple-select"
                                            <?php echo ($invoice->is_read_only == 1 && $invoice->invoice_status_id == 4) ? 'disabled="disabled"' : ''; ?>
                                    >
                                        <option value="0"><?php _trans('select_payment_method'); ?></option>
<?php
foreach ($payment_methods as $payment_method) {
?>
                                        <option <?php check_select($invoice->payment_method, $payment_method->payment_method_id) ?>
                                            value="<?php echo $payment_method->payment_method_id; ?>">
                                            <?php echo $payment_method->payment_method_name; ?>
                                        </option>
<?php
} // End foreach
?>
                                    </select>
                                </div>

                                <div class="invoice-properties">
                                    <label><?php _trans('invoice_password'); ?></label>
                                    <input type="text" id="invoice_password" class="form-control"
                                           value="<?php _htmlsc($invoice->invoice_password); ?>"
                                           <?php echo $invoice->is_read_only ? 'disabled="disabled"' : '';?>>
                                </div>
                            </div>

<?php
$default_custom = false;
$classes = ['control-label', 'controls', '', 'col-xs-12 col-md-6'];
foreach ($custom_fields as $custom_field) {
    if (! $default_custom && ! $custom_field->custom_field_location) {
        $default_custom = true;
    }

    if ($custom_field->custom_field_location == 1) {
        print_field($this->mdl_invoices, $custom_field, $custom_values, $classes[0], $classes[1], $classes[2], $classes[3]);
    }
}
?>

<?php
if ($invoice->invoice_status_id != 1) {
?>
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label for="invoice-guest-url"><?php _trans('guest_url'); ?></label>
                                    <div class="input-group">
                                        <input type="text" id="invoice-guest-url" readonly class="form-control"
                                               value="<?php echo site_url('guest/view/invoice/' . $invoice->invoice_url_key) ?>">
                                        <span class="input-group-addon to-clipboard cursor-pointer"
                                              data-clipboard-target="#invoice-guest-url">
                                            <i class="fa fa-clipboard fa-fw"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
<?php
} // End if
?>

                        </div>
                    </div>
                </div>

            </div>

            <br>

<?php $this->layout->load_view('invoices/partial_itemlist_' . (get_setting('show_responsive_itemlist') ? 'responsive' : 'table')); ?>

            <hr>

            <div class="row">
                <div class="col-xs-12 col-md-6">

                    <div class="panel panel-default no-margin">
                        <div class="panel-heading">
                            <?php _trans('invoice_terms'); ?>
                        </div>
                        <div class="panel-body">
                            <textarea id="invoice_terms" name="invoice_terms" class="form-control" rows="3"
                                      <?php echo $invoice->is_read_only ? 'disabled="disabled"' : '';?>
                            ><?php _htmlsc($invoice->invoice_terms); ?></textarea>
                        </div>
                    </div>

                    <div class="col-xs-12 visible-xs visible-sm"><br></div>

                </div>
                <div class="col-xs-12 col-md-6">

                    <?php _dropzone_html($invoice->is_read_only); ?>

                </div>
            </div>

<?php
if ($default_custom) {
?>
            <div class="row">
                <div class="col-xs-12">

                    <hr>

                    <div class="panel panel-default">
                        <div class="panel-heading"><?php _trans('custom_fields'); ?></div>
                        <div class="panel-body">
                            <div class="row">
<?php
    $classes = ['control-label', 'controls', '', 'form-group col-xs-12 col-sm-6'];
    foreach ($custom_fields as $custom_field) {
        if (! $custom_field->custom_field_location) { // == 0
            print_field($this->mdl_invoices, $custom_field, $custom_values, $classes[0], $classes[1], $classes[2], $classes[3]);
        }
    }
?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
<?php
} // End if custom_fields
?>

        </div>
    </div>
</div>

<?php
_dropzone_script($invoice->invoice_url_key, $invoice->client_id);
